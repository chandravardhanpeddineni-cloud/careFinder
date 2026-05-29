@extends('admin.layout')

@php
    $pageTitle = 'Admin Reviews | CareFinder';
    $topbarSearchAction = route('admin.reviews');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search reviews by patient, doctor, or comment';
    $totalReviews = (int) $ratingCounts->sum();
@endphp

@section('admin-content')
    <section class="admin-modern-welcome">
        <div>
            <p class="eyebrow">Review Moderation</p>
            <h1>Monitor and moderate patient feedback.</h1>
            <p>Use rating filters and search to quickly locate feedback that needs action.</p>
        </div>
        <div class="admin-modern-stats">
            <article>
                <span>Total Reviews</span>
                <strong>{{ $reviewCount }}</strong>
            </article>
            <article>
                <span>5 Star</span>
                <strong>{{ (int) ($ratingCounts[5] ?? 0) }}</strong>
            </article>
            <article>
                <span>1-2 Star</span>
                <strong>{{ (int) ($ratingCounts[1] ?? 0) + (int) ($ratingCounts[2] ?? 0) }}</strong>
            </article>
        </div>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Rating Distribution</p>
                    <h2>Breakdown by Score</h2>
                </div>
            </div>
            <div class="admin-status-tracker">
                @for ($score = 5; $score >= 1; $score--)
                    @php
                        $count = (int) ($ratingCounts[$score] ?? 0);
                        $percent = $totalReviews > 0 ? (int) round(($count / $totalReviews) * 100) : 0;
                    @endphp
                    <div>
                        <div class="label-row"><span>{{ $score }} Star</span><strong>{{ $count }}</strong></div>
                        <div class="progress approved"><span style="width: {{ $percent }}%"></span></div>
                    </div>
                @endfor
            </div>
        </article>

        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Moderation Tips</p>
                    <h2>Quality Signals</h2>
                </div>
            </div>
            <div class="admin-tips-list">
                <article>Prioritize low-rating reviews to identify recurring service issues.</article>
                <article>Review negative comments with appointment context before moderation action.</article>
                <article>Track top-rated doctors to replicate successful care practices.</article>
            </div>
        </article>
    </section>

    <section class="admin-modern-section">
        <div class="admin-modern-section-head">
            <div>
                <p class="eyebrow">All Reviews</p>
                <h2>Patient Feedback Moderation</h2>
            </div>
        </div>

        <div class="admin-filter-chips">
            <a href="{{ route('admin.reviews', ['q' => $search]) }}" class="{{ $ratingFilter === 0 ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.reviews', ['rating' => 5, 'q' => $search]) }}" class="{{ $ratingFilter === 5 ? 'active' : '' }}">5 Star</a>
            <a href="{{ route('admin.reviews', ['rating' => 4, 'q' => $search]) }}" class="{{ $ratingFilter === 4 ? 'active' : '' }}">4 Star</a>
            <a href="{{ route('admin.reviews', ['rating' => 3, 'q' => $search]) }}" class="{{ $ratingFilter === 3 ? 'active' : '' }}">3 Star</a>
            <a href="{{ route('admin.reviews', ['rating' => 2, 'q' => $search]) }}" class="{{ $ratingFilter === 2 ? 'active' : '' }}">2 Star</a>
            <a href="{{ route('admin.reviews', ['rating' => 1, 'q' => $search]) }}" class="{{ $ratingFilter === 1 ? 'active' : '' }}">1 Star</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table-modern">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr>
                            <td>{{ $review->user?->name ?? 'Patient unavailable' }}</td>
                            <td>{{ $review->doctor?->user?->name ?? 'Doctor unavailable' }}</td>
                            <td>{{ $review->rating }}/5</td>
                            <td>{{ $review->comment }}</td>
                            <td>
                                <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="danger-button" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No reviews found for this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
