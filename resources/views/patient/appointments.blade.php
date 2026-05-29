@extends('patient.layout')

@php
    $pageTitle = 'Appointments | CareFinder';
    $pageEyebrow = 'Appointment Center';
    $pageHeading = 'Manage Your Appointments';

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
            <p class="eyebrow">Appointment Tracker</p>
            <h1>Track, filter, and manage your bookings.</h1>
            <p>Use filters to focus on pending, confirmed, or cancelled appointment requests.</p>
        </div>
        <div class="patient-modern-stats">
            <article>
                <span>Upcoming</span>
                <strong>{{ $appointmentSummary['upcoming'] }}</strong>
            </article>
            <article>
                <span>Pending</span>
                <strong>{{ $appointmentSummary['pending'] }}</strong>
            </article>
            <article>
                <span>Cancelled</span>
                <strong>{{ $appointmentSummary['cancelled'] }}</strong>
            </article>
        </div>
    </section>

    <section class="patient-modern-grid">
        <article class="patient-modern-section">
            <div class="patient-modern-section-head">
                <div>
                    <p class="eyebrow">Upcoming</p>
                    <h2>Next Appointments</h2>
                </div>
            </div>
            <div class="patient-appointments-list">
                @forelse ($upcomingAppointments as $appointment)
                    <div class="patient-appointment-item">
                        <div>
                            <h3>{{ $appointment->doctor?->user?->name ?? 'Doctor unavailable' }}</h3>
                            <p>{{ $appointment->doctor?->specialization ?? 'General Care' }}</p>
                            <small>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</small>
                        </div>
                        <div class="patient-appointment-actions">
                            <span class="status-pill {{ strtolower($appointment->status) }}">{{ ucfirst($appointment->status) }}</span>
                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Cancel</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="patient-modern-empty">No upcoming appointments yet.</p>
                @endforelse
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

    <section class="patient-modern-section">
        <div class="patient-modern-section-head">
            <div>
                <p class="eyebrow">History</p>
                <h2>All Appointments</h2>
            </div>
        </div>
        <div class="patient-filter-chips">
            <a href="{{ route('patient.appointments') }}" class="{{ $statusFilter === '' ? 'active' : '' }}">All</a>
            <a href="{{ route('patient.appointments', ['status' => 'pending']) }}" class="{{ $statusFilter === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('patient.appointments', ['status' => 'confirmed']) }}" class="{{ $statusFilter === 'confirmed' ? 'active' : '' }}">Confirmed</a>
            <a href="{{ route('patient.appointments', ['status' => 'cancelled']) }}" class="{{ $statusFilter === 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>

        <div class="patient-appointments-list">
            @forelse ($appointments as $appointment)
                <div class="patient-appointment-item">
                    <div>
                        <h3>{{ $appointment->doctor?->user?->name ?? 'Doctor unavailable' }}</h3>
                        <p>{{ $appointment->doctor?->specialization ?? 'General Care' }}</p>
                        <small>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</small>
                    </div>
                    <div class="patient-appointment-actions">
                        <span class="status-pill {{ strtolower($appointment->status) }}">{{ ucfirst($appointment->status) }}</span>
                        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Cancel</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="patient-modern-empty">No appointments found for this filter.</p>
            @endforelse
        </div>
    </section>
@endsection
