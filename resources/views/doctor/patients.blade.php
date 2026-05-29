@extends('doctor.layout')

@php
    $pageTitle = 'Doctor Patients | CareFinder';
    $topbarSearchAction = route('doctor.patients');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search patients by name';
@endphp

@section('doctor-content')
    @unless ($doctor)
        <section class="doctor-modern-section">
            <p class="doctor-modern-empty">No doctor profile is linked to this account yet. Ask admin to activate your profile.</p>
        </section>
    @else
        <section class="doctor-modern-welcome">
            <div>
                <p class="eyebrow">Patient Center</p>
                <h1>View patient history and recent interactions.</h1>
                <p>Track returning patients and review appointment outcomes from one workspace.</p>
            </div>
            <div class="doctor-modern-stats">
                <article>
                    <span>Recent Patients</span>
                    <strong>{{ $recentPatients->count() }}</strong>
                </article>
                <article>
                    <span>Total Visits</span>
                    <strong>{{ $doctorSummary['total'] }}</strong>
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
                        <p class="eyebrow">Recent Patients</p>
                        <h2>Latest Interactions</h2>
                    </div>
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

            <article class="doctor-modern-section">
                <div class="doctor-modern-section-head">
                    <div>
                        <p class="eyebrow">Recommendations</p>
                        <h2>Care Continuity Tips</h2>
                    </div>
                </div>
                <div class="doctor-tips-list">
                    <article>Encourage follow-up visits for patients with pending treatment plans.</article>
                    <article>Capture concise consultation notes to make next visits faster and clearer.</article>
                    <article>Watch repeat cancellations and re-engage patients with flexible slot options.</article>
                </div>
            </article>
        </section>

        <section class="doctor-modern-section">
            <div class="doctor-modern-section-head">
                <div>
                    <p class="eyebrow">Patient Visit Summary</p>
                    <h2>Aggregated Visit History</h2>
                </div>
            </div>

            <div class="doctor-table-wrap">
                <table class="doctor-table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Email</th>
                            <th>Total Visits</th>
                            <th>Confirmed</th>
                            <th>Last Visit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patientCards as $card)
                            <tr>
                                <td>{{ $card['patient']->name }}</td>
                                <td>{{ $card['patient']->email }}</td>
                                <td>{{ $card['total_visits'] }}</td>
                                <td>{{ $card['confirmed_visits'] }}</td>
                                <td>{{ $card['last_visit'] ? $card['last_visit']->format('M d, Y') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No patient visit data available for this search.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endunless
@endsection
