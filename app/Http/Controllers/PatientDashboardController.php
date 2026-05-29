<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PatientDashboardController extends Controller
{
    public function dashboard(Request $request): View
    {
        $patient = $this->authorizePatient($request);
        $statusCounts = $this->statusCounts($patient);

        return view('patient.dashboard', [
            'appointmentSummary' => $this->appointmentSummary($patient, $statusCounts),
            'statusCounts' => $statusCounts,
            'appointments' => $this->appointmentsQuery($patient)
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->latest('appointment_date')
                ->take(6)
                ->get(),
            'recentReviews' => $patient->reviews()
                ->with('doctor.user')
                ->latest()
                ->take(5)
                ->get(),
            'healthTips' => $this->healthTips(),
        ]);
    }

    public function doctors(Request $request): View
    {
        $patient = $this->authorizePatient($request);
        $search = trim((string) $request->query('q', ''));
        $statusCounts = $this->statusCounts($patient);

        $doctorSearchQuery = Doctor::query()
            ->with('user')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('status', 'approved');

        if ($search !== '') {
            $doctorSearchQuery->where(function ($query) use ($search): void {
                $query
                    ->where('specialization', 'like', "%{$search}%")
                    ->orWhere('hospital', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        return view('patient.doctors', [
            'search' => $search,
            'statusCounts' => $statusCounts,
            'appointmentSummary' => $this->appointmentSummary($patient, $statusCounts),
            'foundDoctors' => (clone $doctorSearchQuery)
                ->latest()
                ->take(10)
                ->get(),
            'topRatedDoctors' => Doctor::query()
                ->with('user')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->where('status', 'approved')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->take(6)
                ->get(),
        ]);
    }

    public function appointments(Request $request): View
    {
        $patient = $this->authorizePatient($request);
        $statusCounts = $this->statusCounts($patient);

        $statusFilter = trim((string) $request->query('status', ''));
        $appointmentsQuery = $this->appointmentsQuery($patient);

        if (in_array($statusFilter, ['pending', 'confirmed', 'cancelled'], true)) {
            $appointmentsQuery->where('status', $statusFilter);
        } else {
            $statusFilter = '';
        }

        return view('patient.appointments', [
            'statusFilter' => $statusFilter,
            'statusCounts' => $statusCounts,
            'appointmentSummary' => $this->appointmentSummary($patient, $statusCounts),
            'upcomingAppointments' => $this->appointmentsQuery($patient)
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->latest('appointment_date')
                ->take(8)
                ->get(),
            'appointments' => $appointmentsQuery
                ->latest('appointment_date')
                ->take(20)
                ->get(),
        ]);
    }

    public function reviews(Request $request): View
    {
        $patient = $this->authorizePatient($request);
        $statusCounts = $this->statusCounts($patient);

        return view('patient.reviews', [
            'statusCounts' => $statusCounts,
            'appointmentSummary' => $this->appointmentSummary($patient, $statusCounts),
            'recentReviews' => $patient->reviews()
                ->with('doctor.user')
                ->latest()
                ->take(20)
                ->get(),
            'topRatedDoctors' => Doctor::query()
                ->with('user')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->where('status', 'approved')
                ->orderByDesc('reviews_avg_rating')
                ->orderByDesc('reviews_count')
                ->take(6)
                ->get(),
        ]);
    }

    public function prescriptions(Request $request): View
    {
        $patient = $this->authorizePatient($request);
        $statusCounts = $this->statusCounts($patient);

        return view('patient.prescriptions', [
            'statusCounts' => $statusCounts,
            'appointmentSummary' => $this->appointmentSummary($patient, $statusCounts),
            'prescriptionAppointments' => $this->appointmentsQuery($patient)
                ->where('status', 'confirmed')
                ->latest('appointment_date')
                ->take(20)
                ->get(),
            'healthTips' => $this->healthTips(),
        ]);
    }

    private function authorizePatient(Request $request): User
    {
        $user = $request->user();

        abort_unless($user?->role === 'patient', 403);

        return $user;
    }

    private function appointmentsQuery(User $patient): Builder
    {
        return Appointment::query()
            ->with('doctor.user')
            ->where('user_id', $patient->id);
    }

    private function statusCounts(User $patient): Collection
    {
        return Appointment::query()
            ->where('user_id', $patient->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function appointmentSummary(User $patient, Collection $statusCounts): array
    {
        return [
            'upcoming' => Appointment::query()
                ->where('user_id', $patient->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->count(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'confirmed' => (int) ($statusCounts['confirmed'] ?? 0),
            'cancelled' => (int) ($statusCounts['cancelled'] ?? 0),
        ];
    }

    private function healthTips(): array
    {
        return [
            'Stay hydrated and aim for 7-8 glasses of water daily.',
            'Walk at least 30 minutes a day to improve heart health.',
            'Keep your routine checkups updated every 6-12 months.',
            'Maintain a consistent sleep cycle for better immunity.',
        ];
    }
}
