@extends('admin.layout')

@php
    $pageTitle = 'Admin Appointments | CareFinder';
    $topbarSearchAction = route('admin.appointments');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search by patient or doctor name';

    $apptPending = (int) ($appointmentStatusCounts['pending'] ?? 0);
    $apptConfirmed = (int) ($appointmentStatusCounts['confirmed'] ?? 0);
    $apptCancelled = (int) ($appointmentStatusCounts['cancelled'] ?? 0);
    $apptTotal = max(1, $apptPending + $apptConfirmed + $apptCancelled);
@endphp

@section('admin-content')
    <section class="admin-modern-welcome">
        <div>
            <p class="eyebrow">Appointment Operations</p>
            <h1>Track and manage booking activity across the platform.</h1>
            <p>Use status filters and inline actions to keep appointment workflows up to date.</p>
        </div>
        <div class="admin-modern-stats">
            <article>
                <span>Total</span>
                <strong>{{ $appointmentCount }}</strong>
            </article>
            <article>
                <span>Confirmed</span>
                <strong>{{ $apptConfirmed }}</strong>
            </article>
            <article>
                <span>Pending</span>
                <strong>{{ $apptPending }}</strong>
            </article>
        </div>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Status Tracker</p>
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
                    <p class="eyebrow">Recent Activity</p>
                    <h2>Latest Appointments</h2>
                </div>
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
                <p class="eyebrow">All Appointments</p>
                <h2>Manage Appointment Status</h2>
            </div>
        </div>

        <div class="admin-filter-chips">
            <a href="{{ route('admin.appointments', ['q' => $search]) }}" class="{{ $statusFilter === '' ? 'active' : '' }}">All</a>
            <a href="{{ route('admin.appointments', ['status' => 'pending', 'q' => $search]) }}" class="{{ $statusFilter === 'pending' ? 'active' : '' }}">Pending</a>
            <a href="{{ route('admin.appointments', ['status' => 'confirmed', 'q' => $search]) }}" class="{{ $statusFilter === 'confirmed' ? 'active' : '' }}">Confirmed</a>
            <a href="{{ route('admin.appointments', ['status' => 'cancelled', 'q' => $search]) }}" class="{{ $statusFilter === 'cancelled' ? 'active' : '' }}">Cancelled</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table-modern">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
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
                            <td>{{ $appointment->doctor?->user?->name ?? 'Doctor unavailable' }}</td>
                            <td>{{ $appointment->appointment_date->format('M d, Y') }}</td>
                            <td>{{ $appointment->appointment_slot ?? 'Not set' }}</td>
                            <td>{{ ucfirst($appointment->status) }}</td>
                            <td>
                                <form class="inline-form" method="POST" action="{{ route('appointments.status', $appointment) }}">
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
                        <tr><td colspan="6">No appointments found for this filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
