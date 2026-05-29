@extends('patient.layout')

@php
    $pageTitle = 'Patient Dashboard | CareFinder';
    $pageEyebrow = 'Patient Workspace';
    $pageHeading = 'Dashboard Overview';

    $pendingCount = (int) ($statusCounts['pending'] ?? 0);
    $confirmedCount = (int) ($statusCounts['confirmed'] ?? 0);
    $cancelledCount = (int) ($statusCounts['cancelled'] ?? 0);
    $statusTotal = max(1, $pendingCount + $confirmedCount + $cancelledCount);
    $pendingPercent = (int) round(($pendingCount / $statusTotal) * 100);
    $confirmedPercent = (int) round(($confirmedCount / $statusTotal) * 100);
    $cancelledPercent = (int) round(($cancelledCount / $statusTotal) * 100);
@endphp

@section('patient-content')
    <section class="patient-modern-welcome">
        <div>
            <p class="eyebrow">Welcome back</p>
            <h1>Manage your healthcare journey with focused modules.</h1>
            <p>
                Use the left navigation to open doctors, appointments, reviews, and prescriptions as separate pages.
            </p>
        </div>
        <div class="patient-modern-stats">
            <article>
                <span>Upcoming</span>
                <strong>{{ $appointmentSummary['upcoming'] }}</strong>
            </article>
            <article>
                <span>Confirmed</span>
                <strong>{{ $appointmentSummary['confirmed'] }}</strong>
            </article>
            <article>
                <span>Pending</span>
                <strong>{{ $appointmentSummary['pending'] }}</strong>
            </article>
        </div>
    </section>

    <section class="patient-modern-grid">
        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Quick Access</p>
                    <h2>Open Patient Modules</h2>
                </div>
            </div>
            <div class="patient-quick-grid">
                <a href="{{ route('patient.doctors') }}">
                    <strong>Find Doctors</strong>
                    <span>Search specialists and hospitals</span>
                </a>
                <a href="{{ route('patient.appointments') }}">
                    <strong>Appointments</strong>
                    <span>Track and manage booking requests</span>
                </a>
                <a href="{{ route('patient.reviews') }}">
                    <strong>Reviews</strong>
                    <span>View feedback you submitted</span>
                </a>
                <a href="{{ route('patient.prescriptions') }}">
                    <strong>Prescriptions</strong>
                    <span>Check consultation records</span>
                </a>
            </div>
        </article>

        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Status Tracker</p>
                    <h2>Appointment Distribution</h2>
                </div>
            </div>
            <div class="patient-status-tracker">
                <div>
                    <div class="label-row">
                        <span>Pending</span>
                        <strong>{{ $pendingCount }}</strong>
                    </div>
                    <div class="progress"><span style="width: {{ $pendingPercent }}%"></span></div>
                </div>
                <div>
                    <div class="label-row">
                        <span>Confirmed</span>
                        <strong>{{ $confirmedCount }}</strong>
                    </div>
                    <div class="progress confirmed"><span style="width: {{ $confirmedPercent }}%"></span></div>
                </div>
                <div>
                    <div class="label-row">
                        <span>Cancelled</span>
                        <strong>{{ $cancelledCount }}</strong>
                    </div>
                    <div class="progress cancelled"><span style="width: {{ $cancelledPercent }}%"></span></div>
                </div>
            </div>
        </article>
    </section>

    <section class="patient-modern-grid">
        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Upcoming</p>
                    <h2>Next Appointments</h2>
                </div>
                <a class="patient-inline-link" href="{{ route('patient.appointments') }}">Open all</a>
            </div>
            <div class="patient-appointments-list">
                @forelse ($appointments as $appointment)
                    <div class="patient-appointment-item">
                        <div>
                            <h3>{{ $appointment->doctor?->user?->name ?? 'Doctor unavailable' }}</h3>
                            <p>{{ $appointment->doctor?->specialization ?? 'General Care' }}</p>
                            <small>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</small>
                        </div>
                        <span class="status-pill {{ strtolower($appointment->status) }}">{{ ucfirst($appointment->status) }}</span>
                    </div>
                @empty
                    <p class="patient-modern-empty">No upcoming appointments yet.</p>
                @endforelse
            </div>
        </article>

        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Recent Reviews</p>
                    <h2>Your Latest Feedback</h2>
                </div>
                <a class="patient-inline-link" href="{{ route('patient.reviews') }}">Open reviews</a>
            </div>
            <div class="patient-reviews-list">
                @forelse ($recentReviews as $review)
                    <div class="patient-review-item">
                        <strong>{{ $review->doctor?->user?->name ?? 'Doctor unavailable' }}</strong>
                        <span>⭐ {{ $review->rating }}/5</span>
                        <p>{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="patient-modern-empty">You have not submitted reviews yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="patient-modern-section">
        <div class="patient-modern-section-head">
            <div>
                <p class="eyebrow">Health Tips</p>
                <h2>Daily Health Recommendations</h2>
            </div>
        </div>
        <div class="patient-health-tips">
            @foreach ($healthTips as $tip)
                <article>{{ $tip }}</article>
            @endforeach
        </div>
    </section>
@endsection
