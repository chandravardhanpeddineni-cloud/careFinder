<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DoctorDashboardController extends Controller
{
    public function dashboard(Request $request): View
    {
        [$doctor, $doctorSummary, $appointmentStats] = $this->doctorContext($request);

        return view('doctor.dashboard', [
            'doctor' => $doctor,
            'doctorSummary' => $doctorSummary,
            'appointmentStats' => $appointmentStats,
            'pendingAppointments' => $this->appointmentsQuery($doctor)
                ->where('status', 'pending')
                ->latest('appointment_date')
                ->take(6)
                ->get(),
            'upcomingAppointments' => $this->appointmentsQuery($doctor)
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->latest('appointment_date')
                ->take(8)
                ->get(),
            'reviews' => $doctor
                ? $doctor->reviews()->with('user')->latest()->take(8)->get()
                : collect(),
            'recentPatients' => $this->recentPatients($doctor, 20, 6),
            'doctorTips' => $this->doctorTips(),
        ]);
    }

    public function appointments(Request $request): View
    {
        [$doctor, $doctorSummary, $appointmentStats] = $this->doctorContext($request);
        $search = trim((string) $request->query('q', ''));
        $statusFilter = trim((string) $request->query('status', ''));
        $validStatuses = ['pending', 'confirmed', 'cancelled'];

        if (! in_array($statusFilter, $validStatuses, true)) {
            $statusFilter = '';
        }

        $appointmentsQuery = $this->appointmentsQuery($doctor);
        $this->applyPatientSearch($appointmentsQuery, $search);

        if ($statusFilter !== '') {
            $appointmentsQuery->where('status', $statusFilter);
        }

        $pendingQuery = $this->appointmentsQuery($doctor)->where('status', 'pending');
        $upcomingQuery = $this->appointmentsQuery($doctor)->whereDate('appointment_date', '>=', now()->toDateString());
        $this->applyPatientSearch($pendingQuery, $search);
        $this->applyPatientSearch($upcomingQuery, $search);

        return view('doctor.appointments', [
            'doctor' => $doctor,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'doctorSummary' => $doctorSummary,
            'appointmentStats' => $appointmentStats,
            'pendingAppointments' => $pendingQuery
                ->latest('appointment_date')
                ->take(10)
                ->get(),
            'upcomingAppointments' => $upcomingQuery
                ->latest('appointment_date')
                ->take(10)
                ->get(),
            'appointments' => $appointmentsQuery
                ->latest('appointment_date')
                ->take(40)
                ->get(),
        ]);
    }

    public function reviews(Request $request): View
    {
        [$doctor, $doctorSummary, $appointmentStats] = $this->doctorContext($request);
        $search = trim((string) $request->query('q', ''));

        $reviewsQuery = $doctor
            ? $doctor->reviews()->with('user')
            : null;

        if ($reviewsQuery && $search !== '') {
            $reviewsQuery->where(function ($query) use ($search): void {
                $query
                    ->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $ratingCounts = $doctor
            ? $doctor->reviews()
                ->selectRaw('rating, count(*) as total')
                ->groupBy('rating')
                ->pluck('total', 'rating')
            : collect();

        return view('doctor.reviews', [
            'doctor' => $doctor,
            'search' => $search,
            'doctorSummary' => $doctorSummary,
            'appointmentStats' => $appointmentStats,
            'reviews' => $reviewsQuery
                ? $reviewsQuery->latest()->take(30)->get()
                : collect(),
            'ratingCounts' => $ratingCounts,
        ]);
    }

    public function patients(Request $request): View
    {
        [$doctor, $doctorSummary, $appointmentStats] = $this->doctorContext($request);
        $search = trim((string) $request->query('q', ''));

        $appointmentsQuery = $this->appointmentsQuery($doctor);
        $this->applyPatientSearch($appointmentsQuery, $search);

        $patientAppointments = $appointmentsQuery
            ->latest('appointment_date')
            ->take(60)
            ->get();

        $patientCards = $patientAppointments
            ->filter(fn ($appointment) => $appointment->user !== null)
            ->groupBy('user_id')
            ->map(function (Collection $group): array {
                $first = $group->first();
                $patient = $first?->user;

                return [
                    'patient' => $patient,
                    'total_visits' => $group->count(),
                    'confirmed_visits' => $group->where('status', 'confirmed')->count(),
                    'last_visit' => optional($group->sortByDesc('appointment_date')->first())->appointment_date,
                ];
            })
            ->filter(fn (array $card) => $card['patient'] !== null)
            ->values();

        return view('doctor.patients', [
            'doctor' => $doctor,
            'search' => $search,
            'doctorSummary' => $doctorSummary,
            'appointmentStats' => $appointmentStats,
            'recentPatients' => $this->recentPatients($doctor, 30, 10, $search),
            'patientCards' => $patientCards,
        ]);
    }

    private function doctorContext(Request $request): array
    {
        $doctor = $this->resolveDoctor($request);
        $appointmentStats = $this->appointmentStats($doctor);

        return [$doctor, $this->doctorSummary($doctor, $appointmentStats), $appointmentStats];
    }

    private function resolveDoctor(Request $request): ?Doctor
    {
        /** @var User|null $user */
        $user = $request->user();

        abort_unless($user?->role === 'doctor', 403);

        return $user->doctor;
    }

    private function appointmentsQuery(?Doctor $doctor): Builder
    {
        if (! $doctor) {
            return Appointment::query()->whereRaw('1 = 0');
        }

        return Appointment::query()
            ->with('user')
            ->where('doctor_id', $doctor->id);
    }

    private function applyPatientSearch(Builder $query, string $search): void
    {
        if ($search === '') {
            return;
        }

        $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
    }

    private function appointmentStats(?Doctor $doctor): Collection
    {
        if (! $doctor) {
            return collect();
        }

        return $doctor->appointments()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    private function doctorSummary(?Doctor $doctor, Collection $appointmentStats): array
    {
        $pending = (int) ($appointmentStats['pending'] ?? 0);
        $confirmed = (int) ($appointmentStats['confirmed'] ?? 0);
        $cancelled = (int) ($appointmentStats['cancelled'] ?? 0);

        return [
            'total' => $pending + $confirmed + $cancelled,
            'pending' => $pending,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'avg_rating' => number_format(
                $doctor ? (float) $doctor->reviews()->avg('rating') : 0.0,
                1
            ),
            'today' => $doctor
                ? $doctor->appointments()->whereDate('appointment_date', now()->toDateString())->count()
                : 0,
        ];
    }

    private function recentPatients(?Doctor $doctor, int $fetchLimit, int $displayLimit, string $search = ''): Collection
    {
        $appointmentsQuery = $this->appointmentsQuery($doctor);
        $this->applyPatientSearch($appointmentsQuery, $search);

        return $appointmentsQuery
            ->latest('appointment_date')
            ->take($fetchLimit)
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->take($displayLimit)
            ->values();
    }

    private function doctorTips(): array
    {
        return [
            'Keep pending requests under 24 hours for better patient trust.',
            'Confirm slots only after checking schedule conflicts.',
            'Encourage patients to share symptoms in notes before consultation.',
            'Review patient feedback weekly to improve care quality.',
        ];
    }
}
