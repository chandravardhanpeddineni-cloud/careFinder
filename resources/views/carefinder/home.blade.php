<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CareFinder</title>
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
                        <small>Healthcare platform</small>
                    </span>
                </a>

                <nav class="public-nav" aria-label="Primary navigation">
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
                        @if (Route::has('register'))
                            <a class="header-action" href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </nav>
            </header>

            <main>
                <section class="public-hero">
                    <div class="hero-copy">
                        <p class="eyebrow">About CareFinder</p>
                        <h1>CareFinder helps patients connect with doctors and manage care online.</h1>
                        <p class="hero-text">
                            This application supports separate dashboards for patients, doctors, and admins.
                            Please login or register to continue.
                        </p>
                        <div class="hero-actions">
                            @auth
                                <a class="primary-link" href="{{ route($dashboardRoute) }}">Open dashboard</a>
                            @else
                                <a class="primary-link" href="{{ route('login') }}">Login</a>
                                @if (Route::has('register'))
                                    <a class="secondary-link" href="{{ route('register') }}">Register</a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    <section class="hero-dashboard" aria-label="CareFinder roles">
                        <div class="overview-list">
                            <div>
                                <span class="list-icon">1</span>
                                <p><strong>Patient dashboard:</strong> appointments and prescriptions.</p>
                            </div>
                            <div>
                                <span class="list-icon">2</span>
                                <p><strong>Doctor dashboard:</strong> appointment requests and reviews.</p>
                            </div>
                            <div>
                                <span class="list-icon">3</span>
                                <p><strong>Admin dashboard:</strong> doctor approvals and platform management.</p>
                            </div>
                        </div>
                    </section>
                </section>

                <section class="public-section">
                    <p class="eyebrow">Core Modules</p>
                    <h2>Everything needed for online care coordination</h2>
                    <div class="feature-grid">
                        <article>
                            <h3>Find Doctors</h3>
                            <p>Browse doctors by specialization, hospital, and location to quickly discover the right provider.</p>
                        </article>
                        <article>
                            <h3>Book Appointments</h3>
                            <p>Select available time slots, add notes, and submit requests directly from the patient dashboard.</p>
                        </article>
                        <article>
                            <h3>Review System</h3>
                            <p>Patients can rate doctors after completed visits to help others make informed decisions.</p>
                        </article>
                        <article>
                            <h3>Doctor Workflow</h3>
                            <p>Doctors can confirm or cancel appointment requests and track upcoming consultations.</p>
                        </article>
                        <article>
                            <h3>Admin Management</h3>
                            <p>Admins can add doctors, update verification status, and moderate platform reviews.</p>
                        </article>
                        <article>
                            <h3>Role-based Access</h3>
                            <p>Each user type gets a dedicated dashboard with features tailored to their responsibilities.</p>
                        </article>
                    </div>
                </section>

                <section class="public-section">
                    <div class="services-section">
                        <div>
                            <p class="eyebrow">How It Works</p>
                            <h2>Simple flow from discovery to follow-up care</h2>
                        </div>
                    </div>
                    <div class="overview-list">
                        <div>
                            <span class="list-icon">1</span>
                            <p><strong>Create account:</strong> Register as a patient, doctor, or admin according to your role.</p>
                        </div>
                        <div>
                            <span class="list-icon">2</span>
                            <p><strong>Access dedicated dashboard:</strong> CareFinder routes each role to its own workspace after login.</p>
                        </div>
                        <div>
                            <span class="list-icon">3</span>
                            <p><strong>Manage healthcare actions:</strong> Bookings, status updates, reviews, and management tools are available by role.</p>
                        </div>
                    </div>
                </section>

                <section class="public-section">
                    <p class="eyebrow">Access</p>
                    <h2>Get started with CareFinder</h2>
                    <div class="patient-summary">
                        <article>
                            <span>Patients</span>
                            <strong>Book & Review</strong>
                            <p>Search doctors, request appointments, and leave feedback after consultation.</p>
                        </article>
                        <article>
                            <span>Doctors</span>
                            <strong>Schedule & Care</strong>
                            <p>Manage appointment requests and monitor patient interactions from one dashboard.</p>
                        </article>
                        <article>
                            <span>Admins</span>
                            <strong>Control Panel</strong>
                            <p>Oversee doctor onboarding, appointment activity, and review moderation.</p>
                        </article>
                    </div>
                    <div class="hero-actions">
                        @auth
                            <a class="primary-link" href="{{ route($dashboardRoute) }}">Open dashboard</a>
                        @else
                            <a class="primary-link" href="{{ route('login') }}">Login</a>
                            @if (Route::has('register'))
                                <a class="secondary-link" href="{{ route('register') }}">Create account</a>
                            @endif
                        @endauth
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
