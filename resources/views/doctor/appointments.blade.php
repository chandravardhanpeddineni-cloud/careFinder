@extends('doctor.layout')

@php
    $pageTitle = 'Doctor Appointments | CareFinder';
    $topbarSearchAction = route('doctor.appointments');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search patients by name';

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
                <p class="eyebrow">Appointment Center</p>
                <h1>Handle requests and control your consultation schedule.</h1>
                <p>Use status filters and search to quickly find patient appointments.</p>
            </div>
            <div class="doctor-modern-stats">
                <article>
                    <span>Total</span>
                    <strong>{{ $doctorSummary['total'] }}</strong>
                </article>
                <article>
                    <span>Pending</span>
                    <strong>{{ $doctorSummary['pending'] }}</strong>
                </article>
                <article>
                    <span>Today</span>
                    <strong>{{ $doctorSummary['today'] }}</strong>
                </article>
            </div>
        </section>

        <section class="doctor-modern-grid">
            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Appointment Requests</p>
                        <h2>Pending Actions</h2>
                    </div>
                </div>

                <div class="doctor-requests-list">
                    @forelse ($pendingAppointments as $appointment)
                        <div class="doctor-request-item">
                            <div>
                                <h3>{{ $appointment->user?->name ?? 'Patient unavailable' }}</h3>
                                <p>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->appointment_slot ?? 'Slot pending' }}</p>
                                @if ($appointment->notes)
                                    <small>Notes: {{ $appointment->notes }}</small>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('appointments.status', $appointment) }}" class="doctor-inline-form">
                                @csrf
                                @method('PATCH')
                                <select name="status">
                                    <option value="pending" @selected($appointment->status === 'pending')>Pending</option>
                                    <option value="confirmed" @selected($appointment->status === 'confirmed')>Confirm</option>
                                    <option value="cancelled" @selected($appointment->status === 'cancelled')>Cancel</option>
                                </select>
                                <button type="submit">Save</button>
                            </form>
                        </div>
                    @empty
                        <p class="doctor-modern-empty">No pending requests at the moment.</p>
                    @endforelse
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

        <section class="doctor-modern-section">
            <div class="doctor-modern-section-head">
                <div>
                    <p class="eyebrow">Upcoming</p>
                    <h2>Upcoming Appointments</h2>
                </div>
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
        </section>

        <section class="doctor-modern-section">
            <div class="doctor-modern-section-head">
                <div>
                    <p class="eyebrow">All Appointments</p>
                    <h2>Complete Appointment List</h2>
                </div>
            </div>

            <div class="doctor-filter-chips">
                <a href="{{ route('doctor.appointments', ['q' => $search]) }}" class="{{ $statusFilter === '' ? 'active' : '' }}">All</a>
                <a href="{{ route('doctor.appointments', ['status' => 'pending', 'q' => $search]) }}" class="{{ $statusFilter === 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('doctor.appointments', ['status' => 'confirmed', 'q' => $search]) }}" class="{{ $statusFilter === 'confirmed' ? 'active' : '' }}">Confirmed</a>
                <a href="{{ route('doctor.appointments', ['status' => 'cancelled', 'q' => $search]) }}" class="{{ $statusFilter === 'cancelled' ? 'active' : '' }}">Cancelled</a>
            </div>

            <div class="doctor-table-wrap">
                <table class="doctor-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Date</th>
                            <th>Slot</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr>
                                <td>{{ $appointment->user?->name ?? 'Patient unavailable' }}</td>
                                <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                                <td>{{ $appointment->appointment_slot ?? 'Not set' }}</td>
                                <td>{{ ucfirst($appointment->status) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('appointments.status', $appointment) }}" class="doctor-inline-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status">
                                            <option value="pending" @selected($appointment->status === 'pending')>Pending</option>
                                            <option value="confirmed" @selected($appointment->status === 'confirmed')>Confirmed</option>
                                            <option value="cancelled" @selected($appointment->status === 'cancelled')>Cancelled</option>
                                        </select>
                                        <button type="submit">Save</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No appointments available for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endunless
@endsection
