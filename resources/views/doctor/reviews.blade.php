@extends('doctor.layout')

@php
    $pageTitle = 'Doctor Reviews | CareFinder';
    $topbarSearchAction = route('doctor.reviews');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search reviews by patient or comment';

    $totalReviews = (int) $ratingCounts->sum();
@endphp

@section('doctor-content')
    @unless ($doctor)
        <section class="doctor-modern-section">
            <p class="doctor-modern-empty">No doctor profile is linked to this account yet. Ask admin to activate your profile.</p>
        </section>
    @else
        <section class="doctor-modern-welcome">
            <div>
                <p class="eyebrow">Review Center</p>
                <h1>Monitor patient feedback and quality trends.</h1>
                <p>Use search to filter feedback and identify care improvement opportunities quickly.</p>
            </div>
            <div class="doctor-modern-stats">
                <article>
                    <span>Total Reviews</span>
                    <strong>{{ $totalReviews }}</strong>
                </article>
                <article>
                    <span>Average Rating</span>
                    <strong>{{ $doctorSummary['avg_rating'] }}</strong>
                </article>
                <article>
                    <span>Confirmed</span>
                    <strong>{{ $doctorSummary['confirmed'] }}</strong>
                </article>
            </div>
        </section>

        <section class="doctor-modern-grid">
            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Rating Breakdown</p>
                        <h2>Distribution by Score</h2>
                    </div>
                </div>
                <div class="doctor-status-tracker">
                    @for ($score = 5; $score >= 1; $score--)
                        @php
                            $count = (int) ($ratingCounts[$score] ?? 0);
                            $percent = $totalReviews > 0 ? (int) round(($count / $totalReviews) * 100) : 0;
                        @endphp
                        <div>
                            <div class="label-row">
                                <span>{{ $score }} Star</span>
                                <strong>{{ $count }}</strong>
                            </div>
                            <div class="progress confirmed"><span style="width: {{ $percent }}%"></span></div>
                        </div>
                    @endfor
                </div>
            </article>

            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Insights</p>
                        <h2>Quick Signals</h2>
                    </div>
                </div>
                <div class="doctor-tips-list">
                    <article>Respond quickly to appointment requests to improve positive feedback.</article>
                    <article>Use consultation notes to reduce follow-up confusion and improve ratings.</article>
                    <article>Track recurring complaints and address them in your next patient interactions.</article>
                </div>
            </article>
        </section>

        <section class="doctor-modern-section">
            <div class="doctor-modern-section-head">
                <div>
                    <p class="eyebrow">Patient Reviews</p>
                    <h2>Recent Feedback</h2>
                </div>
            </div>

            <div class="doctor-reviews-list">
                @forelse ($reviews as $review)
                    <div class="doctor-review-item">
                        <div class="doctor-review-head">
                            <strong>{{ $review->user?->name ?? 'Patient' }}</strong>
                            <span class="status-pill confirmed">⭐ {{ $review->rating }}/5</span>
                        </div>
                        <p>{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="doctor-modern-empty">No reviews found for this search.</p>
                @endforelse
            </div>
        </section>
    @endunless
@endsection
