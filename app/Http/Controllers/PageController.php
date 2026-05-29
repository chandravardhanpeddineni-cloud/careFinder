<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('carefinder.home');
    }

    public function doctor(Doctor $doctor): View
    {
        abort_unless($doctor->status === 'approved' || auth()->user()?->role === 'admin', 404);

        $doctor->load(['user', 'reviews.user'])->loadAvg('reviews', 'rating');

        $canReview = false;
        $existingReview = null;

        if (auth()->check() && auth()->user()?->role === 'patient') {
            $canReview = Appointment::query()
                ->where('user_id', auth()->id())
                ->where('doctor_id', $doctor->id)
                ->where('status', 'confirmed')
                ->whereDate('appointment_date', '<=', now()->toDateString())
                ->exists();

            $existingReview = $doctor->reviews->firstWhere('user_id', auth()->id());
        }

        return view('carefinder.doctor', [
            'doctor' => $doctor,
            'appointmentSlots' => config('carefinder.appointment_slots', []),
            'canReview' => $canReview,
            'existingReview' => $existingReview,
        ]);
    }
}
