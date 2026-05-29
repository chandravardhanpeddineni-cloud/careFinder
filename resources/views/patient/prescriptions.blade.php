@extends('patient.layout')

@php
    $pageTitle = 'Prescriptions | CareFinder';
    $pageEyebrow = 'Prescription Records';
    $pageHeading = 'Medical Records and Follow-up';
@endphp

@section('patient-content')
    <section class="patient-modern-welcome">
        <div>
            <p class="eyebrow">Medical Records</p>
            <h1>Track prescription-ready consultations.</h1>
            <p>
                Confirmed consultations appear here as record placeholders until prescription uploads are added.
            </p>
        </div>
        <div class="patient-modern-stats">
            <article>
                <span>Confirmed Visits</span>
                <strong>{{ $appointmentSummary['confirmed'] }}</strong>
            </article>
            <article>
                <span>Upcoming</span>
                <strong>{{ $appointmentSummary['upcoming'] }}</strong>
            </article>
            <article>
                <span>Pending</span>
                <strong>{{ $appointmentSummary['pending'] }}</strong>
            </article>
        </div>
    </section>

    <section class="patient-modern-section">
        <div class="patient-modern-section-head">
            <div>
                <p class="eyebrow">Prescriptions</p>
                <h2>Recent Prescription Records</h2>
            </div>
        </div>
        <div class="patient-records-list">
            @forelse ($prescriptionAppointments as $appointment)
                <div class="patient-record-item">
                    <div>
                        <strong>{{ $appointment->doctor?->user?->name ?? 'Doctor unavailable' }}</strong>
                        <p>{{ $appointment->appointment_date->format('M d, Y') }} • {{ $appointment->doctor?->hospital ?? 'Hospital pending' }}</p>
                    </div>
                    <span>Prescription pending upload</span>
                </div>
            @empty
                <p class="patient-modern-empty">No prescription records are available yet.</p>
            @endforelse
        </div>
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
