<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>CareFinder</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="preconnect" href="https://images.unsplash.com">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="app-shell">
            <aside class="sidebar" aria-label="Primary navigation">
                <a class="brand" href="{{ url('/') }}">
                    <span class="brand-mark">CF</span>
                    <span>
                        <strong>CareFinder</strong>
                        <small>Patient portal</small>
                    </span>
                </a>

                <nav class="nav-list">
                    <a class="nav-item active" href="#providers" aria-current="page">Providers</a>
                    <a class="nav-item" href="#appointments">Appointments</a>
                    @auth
                        <a class="nav-item" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="nav-item" href="{{ route('login') }}">Login</a>
                    @endauth
                    @if (Route::has('register'))
                        <a class="nav-item" href="{{ route('register') }}">Register</a>
                    @endif
                </nav>

                <section class="support-panel">
                    <span class="panel-label">Care line</span>
                    <strong>24/7 nurse support</strong>
                    <p>Call 1800-CARE-NOW for urgent guidance before visiting a clinic.</p>
                </section>
            </aside>

            <main class="content">
                <header class="topbar">
                    <div>
                        <p class="eyebrow">Find care near you</p>
                        <h1>Book trusted doctors, clinics, and care centers.</h1>
                    </div>
                    @auth
                        <a class="profile-button" href="{{ url('/dashboard') }}" aria-label="Open dashboard">
                            <span>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        </a>
                    @else
                        <a class="profile-button" href="{{ route('login') }}" aria-label="Log in">
                            <span>IN</span>
                        </a>
                    @endauth
                </header>

                <section class="search-panel" aria-label="Search providers">
                    <label class="search-field">
                        <span>Search</span>
                        <input id="searchInput" type="search" placeholder="Doctor, specialty, clinic, or city">
                    </label>

                    <label class="select-field">
                        <span>Specialty</span>
                        <select id="specialtyFilter">
                            <option value="all">All specialties</option>
                            <option value="Primary Care">Primary Care</option>
                            <option value="Cardiology">Cardiology</option>
                            <option value="Pediatrics">Pediatrics</option>
                            <option value="Dermatology">Dermatology</option>
                        </select>
                    </label>

                    <label class="select-field">
                        <span>Availability</span>
                        <select id="availabilityFilter">
                            <option value="all">Any time</option>
                            <option value="Today">Today</option>
                            <option value="Tomorrow">Tomorrow</option>
                            <option value="This week">This week</option>
                        </select>
                    </label>
                </section>

                <section class="dashboard-grid">
                    <section class="providers-section" id="providers" aria-labelledby="providersTitle">
                        <div class="section-heading">
                            <div>
                                <p class="eyebrow">Available care</p>
                                <h2 id="providersTitle">Recommended providers</h2>
                            </div>
                            <span id="resultCount" class="result-count">0 results</span>
                        </div>

                        <div id="providerList" class="provider-list"></div>
                    </section>

                    <aside class="appointment-panel" id="appointments" aria-labelledby="appointmentTitle">
                        <div class="facility-photo" role="img" aria-label="Modern clinic reception"></div>
                        <p class="eyebrow">Next step</p>
                        <h2 id="appointmentTitle">Your appointment</h2>
                        <div id="appointmentSummary" class="empty-state">
                            Select a provider to see appointment details here.
                        </div>
                    </aside>
                </section>
            </main>
        </div>
    </body>
</html>
