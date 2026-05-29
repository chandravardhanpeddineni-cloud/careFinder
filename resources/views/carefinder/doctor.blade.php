<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $doctor->user?->name }} | CareFinder</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="public-page">
            <header class="public-header">
                <a class="brand public-brand" href="{{ route('home') }}">
                    <span class="brand-mark">CF</span>
                    <span>
                        <strong>CareFinder</strong>
                        <small>Doctor profile</small>
                    </span>
                </a>
                <nav class="public-nav">
                    @auth
                        @php
                            $dashboardRoute = match (Auth::user()->role) {
                                'patient' => 'patient.dashboard',
                                'doctor' => 'doctor.dashboard',
                                'admin' => 'admin.dashboard',
                                default => 'dashboard',
                            };
                        @endphp
                        <a class="header-action" href="{{ route($dashboardRoute) }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a class="header-action" href="{{ route('register') }}">Create account</a>
                    @endauth
                </nav>
            </header>

            <main class="doctor-page">
                @if (session('status'))
                    <div class="admin-alert">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="admin-alert error">
                        <strong>Please fix the highlighted fields.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <section class="doctor-profile">
                    <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->user?->name }}">
                    <div>
                        <p class="eyebrow">{{ $doctor->specialization }}</p>
                        <h1>{{ $doctor->user?->name }}</h1>
                        <p class="hero-text">{{ $doctor->about }}</p>
                        <div class="detail-row">
                            <span class="pill">{{ $doctor->qualification }}</span>
                            <span class="pill blue">{{ $doctor->experience }} years</span>
                            <span class="pill amber">₹{{ number_format($doctor->consultation_fee, 0) }}</span>
                            <span class="pill">⭐ {{ number_format((float) ($doctor->reviews_avg_rating ?? 0), 1) }}</span>
                        </div>
                    </div>
                </section>

                <section class="doctor-action-grid">
                    <article class="admin-card">
                        <p class="eyebrow">Book appointment</p>
                        <h2>{{ $doctor->hospital }}</h2>
                        <p class="hero-text">{{ $doctor->location }}</p>

                        @auth
                            @if (Auth::user()->role === 'patient')
                                <form class="auth-form" method="POST" action="{{ route('appointments.store', $doctor) }}">
                                    @csrf
                                    <label>
                                        <span>Appointment date</span>
                                        <input
                                            type="date"
                                            name="appointment_date"
                                            min="{{ now()->toDateString() }}"
                                            max="{{ now()->addMonths(6)->toDateString() }}"
                                            value="{{ old('appointment_date') }}"
                                            required
                                        >
                                    </label>
                                    <label>
                                        <span>Time slot</span>
                                        <select name="appointment_slot" required>
                                            <option value="">Select a slot</option>
                                            @foreach ($appointmentSlots as $slot)
                                                <option value="{{ $slot }}" @selected(old('appointment_slot') === $slot)>
                                                    {{ $slot }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>
                                        <span>Notes (optional)</span>
                                        <textarea name="notes" rows="3" placeholder="Symptoms, concerns, or requests">{{ old('notes') }}</textarea>
                                    </label>
                                    <button class="auth-submit" type="submit">Request appointment</button>
                                </form>
                            @else
                                <p class="dashboard-empty">Only patient accounts can book appointments.</p>
                            @endif
                        @else
                            <a class="primary-link" href="{{ route('login') }}">Login to book</a>
                        @endauth
                    </article>

                    <article class="admin-card">
                        <p class="eyebrow">Reviews</p>
                        <h2>{{ $doctor->reviews->count() }} patient reviews</h2>

                        @auth
                            @if (Auth::user()->role === 'patient' && $canReview)
                                <form class="auth-form" method="POST" action="{{ route('reviews.store', $doctor) }}">
                                    @csrf
                                    <label>
                                        <span>Rating</span>
                                        <select name="rating" required>
                                            <option value="5" @selected((int) old('rating', $existingReview?->rating) === 5)>5 - Excellent</option>
                                            <option value="4" @selected((int) old('rating', $existingReview?->rating) === 4)>4 - Good</option>
                                            <option value="3" @selected((int) old('rating', $existingReview?->rating) === 3)>3 - Average</option>
                                            <option value="2" @selected((int) old('rating', $existingReview?->rating) === 2)>2 - Poor</option>
                                            <option value="1" @selected((int) old('rating', $existingReview?->rating) === 1)>1 - Bad</option>
                                        </select>
                                    </label>
                                    <label>
                                        <span>Comment</span>
                                        <textarea name="comment" rows="4" required>{{ old('comment', $existingReview?->comment) }}</textarea>
                                    </label>
                                    <button class="auth-submit" type="submit">
                                        {{ $existingReview ? 'Update review' : 'Save review' }}
                                    </button>
                                </form>
                            @elseif (Auth::user()->role === 'patient')
                                <p class="dashboard-empty">
                                    Reviews are enabled after at least one confirmed appointment with this doctor.
                                </p>
                            @endif
                        @endauth

                        <div class="review-list">
                            @forelse ($doctor->reviews as $review)
                                <article>
                                    <strong>{{ $review->user?->name }} rated {{ $review->rating }}/5</strong>
                                    <p>{{ $review->comment }}</p>
                                </article>
                            @empty
                                <p class="dashboard-empty">No reviews yet.</p>
                            @endforelse
                        </div>
                    </article>
                </section>
            </main>
        </div>
    </body>
</html>
