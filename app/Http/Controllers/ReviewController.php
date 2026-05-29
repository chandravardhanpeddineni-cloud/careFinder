<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($request->user()?->role === 'patient', 403);
        abort_unless($doctor->status === 'approved', 404);

        $eligibleToReview = Appointment::query()
            ->where('user_id', $request->user()->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'confirmed')
            ->whereDate('appointment_date', '<=', now()->toDateString())
            ->exists();

        if (! $eligibleToReview) {
            return back()->withErrors([
                'rating' => 'You can submit a review only after a confirmed appointment with this doctor.',
            ]);
        }

        $request->merge([
            'comment' => trim((string) $request->comment),
        ]);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $review = Review::updateOrCreate([
            'user_id' => $request->user()->id,
            'doctor_id' => $doctor->id,
        ], [
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('status', $review->wasRecentlyCreated ? 'Review added.' : 'Review updated.');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        abort_unless(
            $request->user()?->role === 'admin' || $review->user_id === $request->user()?->id,
            403
        );

        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
