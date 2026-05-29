@extends('admin.layout')

@php
    $pageTitle = 'Admin Dashboard | CareFinder';
    $pageEyebrow = 'Admin Dashboard';
    $pageHeading = 'Platform Operations';

    $docPending = (int) ($doctorStatusCounts['pending'] ?? 0);
    $docApproved = (int) ($doctorStatusCounts['approved'] ?? 0);
    $docRejected = (int) ($doctorStatusCounts['rejected'] ?? 0);
    $docTotal = max(1, $docPending + $docApproved + $docRejected);

    $apptPending = (int) ($appointmentStatusCounts['pending'] ?? 0);
    $apptConfirmed = (int) ($appointmentStatusCounts['confirmed'] ?? 0);
    $apptCancelled = (int) ($appointmentStatusCounts['cancelled'] ?? 0);
    $apptTotal = max(1, $apptPending + $apptConfirmed + $apptCancelled);
@endphp

@section('admin-content')
    <section class="admin-modern-overview">
        <article>
            <span>Doctors</span>
            <strong>{{ $doctorCount }}</strong>
            <p>Total doctor profiles</p>
        </article>
        <article>
            <span>Patients</span>
            <strong>{{ $patientCount }}</strong>
            <p>Registered patient users</p>
        </article>
        <article>
            <span>Appointments</span>
            <strong>{{ $appointmentCount }}</strong>
            <p>System-wide bookings</p>
        </article>
        <article>
            <span>Reviews</span>
            <strong>{{ $reviewCount }}</strong>
            <p>Patient feedback entries</p>
        </article>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Quick Access</p>
                    <h2>Open Admin Modules</h2>
                </div>
            </div>
            <div class="admin-quick-grid">
                <a href="{{ route('admin.doctors') }}">
                    <strong>Doctors</strong>
                    <span>Add and manage doctor profiles</span>
                </a>
                <a href="{{ route('admin.appointments') }}">
                    <strong>Appointments</strong>
                    <span>Monitor and update booking status</span>
                </a>
                <a href="{{ route('admin.reviews') }}">
                    <strong>Reviews</strong>
                    <span>Moderate patient feedback</span>
                </a>
                <a href="{{ route('profile.edit') }}">
                    <strong>Settings</strong>
                    <span>Manage admin account preferences</span>
                </a>
            </div>
        </article>

        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Doctor Status</p>
                    <h2>Approval Distribution</h2>
                </div>
            </div>
            <div class="admin-status-tracker">
                <div>
                    <div class="label-row"><span>Approved</span><strong>{{ $docApproved }}</strong></div>
                    <div class="progress approved"><span style="width: {{ (int) round(($docApproved / $docTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Pending</span><strong>{{ $docPending }}</strong></div>
                    <div class="progress pending"><span style="width: {{ (int) round(($docPending / $docTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Rejected</span><strong>{{ $docRejected }}</strong></div>
                    <div class="progress rejected"><span style="width: {{ (int) round(($docRejected / $docTotal) * 100) }}%"></span></div>
                </div>
            </div>
        </article>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Appointments</p>
                    <h2>Booking Distribution</h2>
                </div>
            </div>
            <div class="admin-status-tracker">
                <div>
                    <div class="label-row"><span>Confirmed</span><strong>{{ $apptConfirmed }}</strong></div>
                    <div class="progress approved"><span style="width: {{ (int) round(($apptConfirmed / $apptTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Pending</span><strong>{{ $apptPending }}</strong></div>
                    <div class="progress pending"><span style="width: {{ (int) round(($apptPending / $apptTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Cancelled</span><strong>{{ $apptCancelled }}</strong></div>
                    <div class="progress rejected"><span style="width: {{ (int) round(($apptCancelled / $apptTotal) * 100) }}%"></span></div>
                </div>
            </div>
        </article>

        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Recommendations</p>
                    <h2>Admin Focus Areas</h2>
                </div>
            </div>
            <div class="admin-tips-list">
                @foreach ($adminTips as $tip)
                    <article>{{ $tip }}</article>
                @endforeach
            </div>
        </article>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Recent Doctors</p>
                    <h2>Latest Added Profiles</h2>
                </div>
                <a class="admin-inline-link" href="{{ route('admin.doctors') }}">Open doctors</a>
            </div>
            <div class="admin-recent-list">
                @forelse ($recentDoctors as $doctor)
                    <div class="admin-recent-item">
                        <strong>{{ $doctor->user?->name ?? 'Doctor' }}</strong>
                        <p>{{ $doctor->specialization }} • {{ $doctor->hospital }}</p>
                        <span class="status-pill {{ $doctor->status }}">{{ ucfirst($doctor->status) }}</span>
                    </div>
                @empty
                    <p class="admin-modern-empty">No doctors found.</p>
                @endforelse
            </div>
        </article>

        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Recent Appointments</p>
                    <h2>Latest Booking Activity</h2>
                </div>
                <a class="admin-inline-link" href="{{ route('admin.appointments') }}">Open appointments</a>
            </div>
            <div class="admin-recent-list">
                @forelse ($recentAppointments as $appointment)
                    <div class="admin-recent-item">
                        <strong>{{ $appointment->user?->name ?? 'Patient' }} → {{ $appointment->doctor?->user?->name ?? 'Doctor' }}</strong>
                        <p>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</p>
                        <span class="status-pill {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                    </div>
                @empty
                    <p class="admin-modern-empty">No appointment activity yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="admin-modern-section">
        <div class="admin-modern-section-head">
            <div>
                <p class="eyebrow">Recent Reviews</p>
                <h2>Latest Patient Feedback</h2>
            </div>
            <a class="admin-inline-link" href="{{ route('admin.reviews') }}">Open reviews</a>
        </div>
        <div class="admin-recent-list">
            @forelse ($recentReviews as $review)
                <div class="admin-recent-item">
                    <strong>{{ $review->user?->name ?? 'Patient' }} rated {{ $review->rating }}/5</strong>
                    <p>{{ $review->doctor?->user?->name ?? 'Doctor unavailable' }} • {{ $review->comment }}</p>
                </div>
            @empty
                <p class="admin-modern-empty">No reviews found.</p>
            @endforelse
        </div>
    </section>
@endsection
