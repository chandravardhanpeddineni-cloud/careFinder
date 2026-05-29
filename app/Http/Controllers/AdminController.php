<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $this->authorizeAdmin();

        return view('admin.dashboard', $this->baseOverviewData() + [
            'recentAppointments' => Appointment::with(['user', 'doctor.user'])
                ->latest('appointment_date')
                ->take(8)
                ->get(),
            'recentDoctors' => Doctor::with('user')->latest()->take(6)->get(),
            'recentReviews' => Review::with(['user', 'doctor.user'])->latest()->take(6)->get(),
            'adminTips' => $this->adminTips(),
        ]);
    }

    public function doctors(Request $request): View
    {
        $this->authorizeAdmin();

        $search = trim((string) $request->query('q', ''));

        $doctorsQuery = Doctor::query()->with('user');

        if ($search !== '') {
            $doctorsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('specialization', 'like', "%{$search}%")
                    ->orWhere('hospital', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return view('admin.doctors', $this->baseOverviewData() + [
            'search' => $search,
            'recentDoctors' => Doctor::with('user')->latest()->take(6)->get(),
            'doctors' => $doctorsQuery->latest()->take(80)->get(),
        ]);
    }

    public function appointments(Request $request): View
    {
        $this->authorizeAdmin();

        $search = trim((string) $request->query('q', ''));
        $statusFilter = trim((string) $request->query('status', ''));
        $validStatuses = ['pending', 'confirmed', 'cancelled'];

        if (! in_array($statusFilter, $validStatuses, true)) {
            $statusFilter = '';
        }

        $appointmentsQuery = Appointment::query()->with(['user', 'doctor.user']);

        if ($search !== '') {
            $appointmentsQuery->where(function ($query) use ($search): void {
                $query
                    ->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($doctorUserQuery) => $doctorUserQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($statusFilter !== '') {
            $appointmentsQuery->where('status', $statusFilter);
        }

        return view('admin.appointments', $this->baseOverviewData() + [
            'search' => $search,
            'statusFilter' => $statusFilter,
            'recentAppointments' => Appointment::with(['user', 'doctor.user'])
                ->latest('appointment_date')
                ->take(10)
                ->get(),
            'appointments' => $appointmentsQuery->latest('appointment_date')->take(80)->get(),
        ]);
    }

    public function reviews(Request $request): View
    {
        $this->authorizeAdmin();

        $search = trim((string) $request->query('q', ''));
        $ratingFilter = (int) $request->query('rating', 0);

        $reviewsQuery = Review::query()->with(['user', 'doctor.user']);

        if ($search !== '') {
            $reviewsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($doctorUserQuery) => $doctorUserQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($ratingFilter >= 1 && $ratingFilter <= 5) {
            $reviewsQuery->where('rating', $ratingFilter);
        } else {
            $ratingFilter = 0;
        }

        $ratingCounts = Review::query()
            ->selectRaw('rating, count(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        return view('admin.reviews', $this->baseOverviewData() + [
            'search' => $search,
            'ratingFilter' => $ratingFilter,
            'ratingCounts' => $ratingCounts,
            'reviews' => $reviewsQuery->latest()->take(100)->get(),
        ]);
    }

    public function storeDoctor(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'specialization' => ['required', 'string', 'max:255'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience' => ['required', 'integer', 'min:0', 'max:80'],
            'hospital' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'consultation_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'profile_image' => ['nullable', 'url', 'max:2048'],
            'about' => ['required', 'string', 'max:3000'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
        ]);

        Doctor::create([
            'user_id' => $user->id,
            'specialization' => $validated['specialization'],
            'qualification' => $validated['qualification'],
            'experience' => $validated['experience'],
            'hospital' => $validated['hospital'],
            'location' => $validated['location'],
            'consultation_fee' => $validated['consultation_fee'],
            'profile_image' => $validated['profile_image'] ?: 'https://images.unsplash.com/photo-1582750433449-648ed127bb54?auto=format&fit=crop&w=600&q=80',
            'about' => $validated['about'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.doctors')
            ->with('status', 'Doctor added successfully.');
    }

    public function updateDoctorStatus(Request $request, Doctor $doctor): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);

        $doctor->update($validated);

        return redirect()
            ->route('admin.doctors')
            ->with('status', 'Doctor status updated.');
    }

    private function baseOverviewData(): array
    {
        return [
            'doctorCount' => Doctor::count(),
            'patientCount' => User::where('role', 'patient')->count(),
            'appointmentCount' => Appointment::count(),
            'reviewCount' => Review::count(),
            'doctorStatusCounts' => Doctor::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'appointmentStatusCounts' => Appointment::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ];
    }

    private function adminTips(): array
    {
        return [
            'Review pending doctor profiles daily to avoid onboarding delays.',
            'Monitor confirmed appointment trends to plan doctor capacity.',
            'Resolve flagged reviews quickly to maintain trust in platform quality.',
        ];
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->role === 'admin', 403);
    }
}
