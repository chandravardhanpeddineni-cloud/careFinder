@extends('patient.layout')

@php
    $pageTitle = 'Reviews | CareFinder';
    $pageEyebrow = 'Review Center';
    $pageHeading = 'Your Feedback and Ratings';
@endphp

@section('patient-content')
    <section class="patient-modern-welcome">
        <div>
            <p class="eyebrow">Patient Feedback</p>
            <h1>Review your submitted doctor ratings.</h1>
            <p>
                Ratings help other patients choose doctors and help providers improve care quality.
            </p>
        </div>
        <div class="patient-modern-stats">
            <article>
                <span>Total Reviews</span>
                <strong>{{ $recentReviews->count() }}</strong>
            </article>
            <article>
                <span>Confirmed</span>
                <strong>{{ $appointmentSummary['confirmed'] }}</strong>
            </article>
            <article>
                <span>Upcoming</span>
                <strong>{{ $appointmentSummary['upcoming'] }}</strong>
            </article>
        </div>
    </section>

    <section class="patient-modern-grid">
        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Your Reviews</p>
                    <h2>Recent Reviews by You</h2>
                </div>
            </div>
            <div class="patient-reviews-list">
                @forelse ($recentReviews as $review)
                    <div class="patient-review-item">
                        <div class="patient-review-head">
                            <strong>{{ $review->doctor?->user?->name ?? 'Doctor unavailable' }}</strong>
                            <span>⭐ {{ $review->rating }}/5</span>
                        </div>
                        <p>{{ $review->comment }}</p>
                        <div class="patient-review-actions">
                            @if ($review->doctor)
                                <a class="patient-inline-link" href="{{ route('doctors.show', $review->doctor) }}">Open doctor profile</a>
                                <details class="patient-review-edit">
                                    <summary>Edit review</summary>
                                    <form class="patient-review-edit-form" method="POST" action="{{ route('reviews.store', $review->doctor) }}">
                                        @csrf
                                        <label>
                                            <span>Rating</span>
                                            <select name="rating" required>
                                                <option value="5" @selected((int) $review->rating === 5)>5 - Excellent</option>
                                                <option value="4" @selected((int) $review->rating === 4)>4 - Good</option>
                                                <option value="3" @selected((int) $review->rating === 3)>3 - Average</option>
                                                <option value="2" @selected((int) $review->rating === 2)>2 - Poor</option>
                                                <option value="1" @selected((int) $review->rating === 1)>1 - Bad</option>
                                            </select>
                                        </label>
                                        <label class="full">
                                            <span>Comment</span>
                                            <textarea name="comment" rows="3" required>{{ $review->comment }}</textarea>
                                        </label>
                                        <button type="submit">Update review</button>
                                    </form>
                                </details>
                            @else
                                <small>Doctor profile is unavailable for editing this review.</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="patient-modern-empty">You have not submitted reviews yet.</p>
                @endforelse
            </div>
        </article>

        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Discover Doctors</p>
                    <h2>Top-Rated Doctors</h2>
                </div>
            </div>
            <div class="patient-top-rated-grid">
                @forelse ($topRatedDoctors as $doctor)
                    <article>
                        <h3>{{ $doctor->user?->name }}</h3>
                        <p>{{ $doctor->specialization }} • {{ $doctor->hospital }}</p>
                        <div class="rating-row">
                            <span>⭐ {{ number_format((float) ($doctor->reviews_avg_rating ?? 0), 1) }}</span>
                            <span>{{ $doctor->reviews_count }} reviews</span>
                        </div>
                        <a class="patient-inline-link" href="{{ route('doctors.show', $doctor) }}">Open profile</a>
                    </article>
                @empty
                    <p class="patient-modern-empty">Top-rated doctor data is not available yet.</p>
                @endforelse
            </div>
        </article>
    </section>
@endsection
