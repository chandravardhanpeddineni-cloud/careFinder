@extends('patient.layout')

@php
    $pageTitle = 'Find Doctors | CareFinder';
    $topbarSearchAction = route('patient.doctors');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search doctors by specialization, hospital, or location';
@endphp

@section('patient-content')
    <section class="patient-modern-welcome">
        <div>
            <p class="eyebrow">Doctor Discovery</p>
            <h1>Find the right doctor for your consultation.</h1>
            <p>
                Search across specialization, hospital, and location, then open a profile to book an appointment.
            </p>
        </div>
        <div class="patient-modern-stats">
            <article>
                <span>Search Results</span>
                <strong>{{ $foundDoctors->count() }}</strong>
            </article>
            <article>
                <span>Upcoming</span>
                <strong>{{ $appointmentSummary['upcoming'] }}</strong>
            </article>
            <article>
                <span>Confirmed</span>
                <strong>{{ $appointmentSummary['confirmed'] }}</strong>
            </article>
        </div>
    </section>

    <section class="patient-modern-section">
        <div class="patient-modern-section-head">
            <div>
                <p class="eyebrow">Find Doctors</p>
                <h2>Doctor Search Results</h2>
            </div>
            <a class="patient-inline-link" href="{{ route('patient.doctors') }}">Clear search</a>
        </div>

        <div class="patient-doctor-grid">
            @forelse ($foundDoctors as $doctor)
                <article class="patient-doctor-card">
                    <img src="{{ $doctor->profile_image }}" alt="{{ $doctor->user?->name }}">
                    <div class="patient-doctor-meta">
                        <h3>{{ $doctor->user?->name }}</h3>
                        <p>{{ $doctor->specialization }}</p>
                        <div class="patient-doctor-tags">
                            <span>{{ $doctor->experience }} yrs exp</span>
                            <span>{{ $doctor->hospital }}</span>
                            <span>{{ $doctor->location }}</span>
                        </div>
                        <div class="patient-doctor-footer">
                            <div>
                                <strong>⭐ {{ number_format((float) ($doctor->reviews_avg_rating ?? 0), 1) }}</strong>
                                <small>{{ $doctor->reviews_count }} reviews</small>
                            </div>
                            <div>
                                <strong>₹{{ number_format($doctor->consultation_fee, 0) }}</strong>
                                <small>Consultation fee</small>
                            </div>
                        </div>
                        <a class="patient-book-btn" href="{{ route('doctors.show', $doctor) }}">Book Appointment</a>
                    </div>
                </article>
            @empty
                <p class="patient-modern-empty">No doctors matched your search. Try another specialization or location.</p>
            @endforelse
        </div>
    </section>

    <section class="patient-modern-section">
        <div class="patient-modern-section-head">
            <div>
                <p class="eyebrow">Top Rated</p>
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
    </section>
@endsection
