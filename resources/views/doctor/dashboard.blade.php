@extends('doctor.layout')

@php
    $pageTitle = 'Doctor Dashboard | CareFinder';
    $pageEyebrow = 'Doctor Workspace';
    $pageHeading = 'Dashboard Overview';

    $statusTotal = max(1, (int) $doctorSummary['total']);
    $pendingPercent = (int) round(((int) $doctorSummary['pending'] / $statusTotal) * 100);
    $confirmedPercent = (int) round(((int) $doctorSummary['confirmed'] / $statusTotal) * 100);
    $cancelledPercent = (int) round(((int) $doctorSummary['cancelled'] / $statusTotal) * 100);
@endphp

@section('doctor-content')
    @unless ($doctor)
        <section class="doctor-modern-section">
            <p class="doctor-modern-empty">No doctor profile is linked to this account yet. Ask admin to activate your profile.</p>
        </section>
    @else
        <section class="doctor-modern-welcome">
            <div>
                <p class="eyebrow">Doctor Workspace</p>
                <h1>Manage patient requests, schedule visits, and track care quality.</h1>
                <p>
                    {{ $doctor->hospital }} • {{ $doctor->location }} • Status: {{ ucfirst($doctor->status) }}
                </p>
            </div>
            <div class="doctor-modern-stats">
                <article>
                    <span>Appointments</span>
                    <strong>{{ $doctorSummary['total'] }}</strong>
                </article>
                <article>
                    <span>Today</span>
                    <strong>{{ $doctorSummary['today'] }}</strong>
                </article>
                <article>
                    <span>Avg Rating</span>
                    <strong>{{ $doctorSummary['avg_rating'] }}</strong>
                </article>
            </div>
        </section>

        <section class="doctor-modern-grid">
            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Quick Access</p>
                        <h2>Open Doctor Modules</h2>
                    </div>
                </div>
                <div class="doctor-quick-grid">
                    <a href="{{ route('doctor.appointments') }}">
                        <strong>Appointments</strong>
                        <span>Manage requests and schedules</span>
                    </a>
                    <a href="{{ route('doctor.reviews') }}">
                        <strong>Reviews</strong>
                        <span>Track patient feedback trends</span>
                    </a>
                    <a href="{{ route('doctor.patients') }}">
                        <strong>Patients</strong>
                        <span>Browse recent patient history</span>
                    </a>
                    <a href="{{ route('profile.edit') }}">
                        <strong>Settings</strong>
                        <span>Update your account profile</span>
                    </a>
                </div>
            </article>

            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Status Tracker</p>
                        <h2>Appointment Distribution</h2>
                    </div>
                </div>
                <div class="doctor-status-tracker">
                    <div>
                        <div class="label-row">
                            <span>Pending</span>
                            <strong>{{ $doctorSummary['pending'] }}</strong>
                        </div>
                        <div class="progress"><span style="width: {{ $pendingPercent }}%"></span></div>
                    </div>
                    <div>
                        <div class="label-row">
                            <span>Confirmed</span>
                            <strong>{{ $doctorSummary['confirmed'] }}</strong>
                        </div>
                        <div class="progress confirmed"><span style="width: {{ $confirmedPercent }}%"></span></div>
                    </div>
                    <div>
                        <div class="label-row">
                            <span>Cancelled</span>
                            <strong>{{ $doctorSummary['cancelled'] }}</strong>
                        </div>
                        <div class="progress cancelled"><span style="width: {{ $cancelledPercent }}%"></span></div>
                    </div>
                </div>
            </article>
        </section>

        <section class="doctor-modern-grid">
            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Appointment Requests</p>
                        <h2>Pending Actions</h2>
                    </div>
                    <a class="doctor-inline-link" href="{{ route('doctor.appointments') }}">Open all</a>
                </div>

                <div class="doctor-requests-list">
                    @forelse ($pendingAppointments as $appointment)
                        <div class="doctor-request-item">
                            <div>
                                <h3>{{ $appointment->user?->name ?? 'Patient unavailable' }}</h3>
                                <p>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</p>
                            </div>
                            <span class="status-pill pending">Pending</span>
                        </div>
                    @empty
                        <p class="doctor-modern-empty">No pending requests at the moment.</p>
                    @endforelse
                </div>
            </article>

            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Schedule</p>
                        <h2>Upcoming Appointments</h2>
                    </div>
                    <a class="doctor-inline-link" href="{{ route('doctor.appointments') }}">Open schedule</a>
                </div>

                <div class="doctor-schedule-list">
                    @forelse ($upcomingAppointments as $appointment)
                        <div class="doctor-schedule-item">
                            <div>
                                <strong>{{ $appointment->user?->name ?? 'Patient unavailable' }}</strong>
                                <p>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</p>
                            </div>
                            <span class="status-pill {{ strtolower($appointment->status) }}">{{ ucfirst($appointment->status) }}</span>
                        </div>
                    @empty
                        <p class="doctor-modern-empty">No upcoming appointments found.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="doctor-modern-grid">
            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Patient Reviews</p>
                        <h2>Recent Feedback</h2>
                    </div>
                    <a class="doctor-inline-link" href="{{ route('doctor.reviews') }}">Open reviews</a>
                </div>

                <div class="doctor-reviews-list">
                    @forelse ($reviews as $review)
                        <div class="doctor-review-item">
                            <strong>{{ $review->user?->name ?? 'Patient' }} rated {{ $review->rating }}/5</strong>
                            <p>{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="doctor-modern-empty">No reviews yet.</p>
                    @endforelse
                </div>
            </article>

            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Patients</p>
                        <h2>Recent Patients</h2>
                    </div>
                    <a class="doctor-inline-link" href="{{ route('doctor.patients') }}">Open patients</a>
                </div>

                <div class="doctor-patients-list">
                    @forelse ($recentPatients as $patient)
                        <div class="doctor-patient-item">
                            <span>{{ strtoupper(substr($patient->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $patient->name }}</strong>
                                <small>{{ $patient->email }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="doctor-modern-empty">No patient history available yet.</p>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="doctor-modern-section">
            <div class="doctor-modern-section-head">
                <div>
                    <p class="eyebrow">Recommendations</p>
                    <h2>Practice Tips</h2>
                </div>
            </div>
            <div class="doctor-tips-list">
                @foreach ($doctorTips as $tip)
                    <article>{{ $tip }}</article>
                @endforeach
            </div>
        </section>
    @endunless
@endsection
