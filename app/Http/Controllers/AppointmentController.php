<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user?->role === 'patient', 403);
        abort_unless($doctor->status === 'approved', 404);

        $appointmentSlots = config('carefinder.appointment_slots', []);
        $maxBookingDate = now()->addMonths(6)->toDateString();

        $validated = $request->validate([
            'appointment_date' => ['required', 'date', 'after_or_equal:today', "before_or_equal:{$maxBookingDate}"],
            'appointment_slot' => ['required', 'string', Rule::in($appointmentSlots)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $activeStatuses = ['pending', 'confirmed'];

        $patientConflict = Appointment::query()
            ->where('user_id', $user->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_slot', $validated['appointment_slot'])
            ->whereIn('status', $activeStatuses)
            ->exists();

        if ($patientConflict) {
            return back()
                ->withErrors(['appointment_slot' => 'You already have another active appointment in this slot.'])
                ->withInput();
        }

        $doctorConflict = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_slot', $validated['appointment_slot'])
            ->whereIn('status', $activeStatuses)
            ->exists();

        if ($doctorConflict) {
            return back()
                ->withErrors(['appointment_slot' => 'This doctor is already booked for the selected slot.'])
                ->withInput();
        }

        $existing = Appointment::query()
            ->where('user_id', $user->id)
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_slot', $validated['appointment_slot'])
            ->first();

        $notes = trim((string) ($validated['notes'] ?? ''));

        $payload = [
            'appointment_date' => $validated['appointment_date'],
            'appointment_slot' => $validated['appointment_slot'],
            'notes' => $notes !== '' ? $notes : null,
            'status' => 'pending',
        ];

        if ($existing && $existing->status === 'cancelled') {
            $existing->update($payload);

            return redirect()
                ->route('dashboard')
                ->with('status', 'Cancelled appointment was re-requested for this slot.');
        }

        if ($existing) {
            return back()
                ->withErrors(['appointment_slot' => 'An appointment request for this slot already exists.'])
                ->withInput();
        }

        Appointment::create($payload + [
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Appointment request sent.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $user?->role === 'admin' || ($user?->role === 'doctor' && $appointment->doctor?->user_id === $user->id),
            403
        );

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled'])],
        ]);

        $nextStatus = $validated['status'];

        if ($user?->role === 'doctor') {
            $allowedTransitions = [
                'pending' => ['pending', 'confirmed', 'cancelled'],
                'confirmed' => ['confirmed', 'cancelled'],
                'cancelled' => ['cancelled'],
            ];

            if (! in_array($nextStatus, $allowedTransitions[$appointment->status] ?? [], true)) {
                return back()->withErrors([
                    'status' => 'Doctors cannot move this appointment to the selected status.',
                ]);
            }
        }

        if ($nextStatus === 'confirmed' && filled($appointment->appointment_slot)) {
            $slotConflict = Appointment::query()
                ->where('doctor_id', $appointment->doctor_id)
                ->whereDate('appointment_date', $appointment->appointment_date)
                ->where('appointment_slot', $appointment->appointment_slot)
                ->where('status', 'confirmed')
                ->where('id', '!=', $appointment->id)
                ->exists();

            if ($slotConflict) {
                return back()->withErrors([
                    'status' => 'This slot is already confirmed for another patient.',
                ]);
            }
        }

        $appointment->update(['status' => $nextStatus]);

        return back()->with('status', 'Appointment status updated.');
    }

    public function destroy(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless(
            $request->user()?->role === 'admin' || $appointment->user_id === $request->user()?->id,
            403
        );

        $appointment->delete();

        return back()->with('status', 'Appointment cancelled.');
    }
}
