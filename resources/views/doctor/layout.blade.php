<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ?? 'Doctor Dashboard | CareFinder' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('doctor-modern.css') }}">
    </head>
    <body>
        @php
            $showSearch = isset($topbarSearchAction) && $topbarSearchAction !== '';
            $topbarSearchInputName = $topbarSearchInputName ?? 'q';
            $topbarSearchValue = $topbarSearchValue ?? '';
            $topbarSearchPlaceholder = $topbarSearchPlaceholder ?? 'Search';
            $profileLabel = $doctor?->specialization ?? 'Doctor';
        @endphp

        <div class="doctor-modern-page">
            <aside class="doctor-modern-sidebar">
                <a class="doctor-modern-brand" href="{{ route('doctor.dashboard') }}">
                    <span class="doctor-modern-brand-mark">CF</span>
                    <div>
                        <strong>CareFinder</strong>
                        <small>Doctor Portal</small>
                    </div>
                </a>

                <nav class="doctor-modern-nav">
                    <a href="{{ route('doctor.dashboard') }}" class="{{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('doctor.appointments') }}" class="{{ request()->routeIs('doctor.appointments') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 15H5V10h14v9Zm0-11H5V6h14v2Z"/></svg>
                        <span>Appointments</span>
                    </a>
                    <a href="{{ route('doctor.reviews') }}" class="{{ request()->routeIs('doctor.reviews') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.3 18.2 21l-1.7-7.1L22 9.2l-7.2-.6L12 2 9.2 8.6 2 9.2l5.5 4.7L5.8 21 12 17.3Z"/></svg>
                        <span>Reviews</span>
                    </a>
                    <a href="{{ route('doctor.patients') }}" class="{{ request()->routeIs('doctor.patients') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3ZM8 11c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5 5 6.34 5 8s1.34 3 3 3Zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5C15 14.17 10.33 13 8 13Zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.98 1.97 3.45V19H23v-2.5c0-2.33-4.67-3.5-7-3.5Z"/></svg>
                        <span>Patients</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m19.14 12.94.86-1.49-1.5-2.6-1.73.18a5.9 5.9 0 0 0-1.03-.6L15.5 6h-3l-.24 2.43c-.36.15-.7.35-1.02.6l-1.74-.18-1.5 2.6.86 1.49c-.03.2-.06.4-.06.61 0 .21.03.41.06.61l-.86 1.49 1.5 2.6 1.74-.18c.32.25.66.45 1.02.6L12.5 22h3l.24-2.43c.36-.15.7-.35 1.03-.6l1.73.18 1.5-2.6-.86-1.49c.03-.2.06-.4.06-.61 0-.21-.03-.41-.06-.61ZM14 16a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z"/></svg>
                        <span>Settings</span>
                    </a>
                </nav>
            </aside>

            <div class="doctor-modern-main">
                <header class="doctor-modern-topbar">
                    <a class="doctor-mobile-brand" href="{{ route('doctor.dashboard') }}">CareFinder</a>

                    @if ($showSearch)
                        <form method="GET" action="{{ $topbarSearchAction }}" class="doctor-modern-search">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79L20 21.5 21.5 20l-6-6Zm-6 0a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/></svg>
                            <input
                                type="text"
                                name="{{ $topbarSearchInputName }}"
                                value="{{ $topbarSearchValue }}"
                                placeholder="{{ $topbarSearchPlaceholder }}"
                            >
                            <button type="submit">Search</button>
                        </form>
                    @else
                        <div class="doctor-topbar-title">
                            <p class="eyebrow">{{ $pageEyebrow ?? 'Doctor Workspace' }}</p>
                            <h1>{{ $pageHeading ?? 'CareFinder Dashboard' }}</h1>
                        </div>
                    @endif

                    <div class="doctor-modern-top-actions">
                        <div class="doctor-modern-profile">
                            <span class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ Auth::user()->name }}</strong>
                                <small>{{ $profileLabel }}</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="doctor-logout-btn" type="submit">Logout</button>
                        </form>
                    </div>
                </header>

                <main class="doctor-modern-content">
                    @if (session('status'))
                        <div class="doctor-modern-alert">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="doctor-modern-alert doctor-modern-alert-error">
                            <strong>Please fix the highlighted fields.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('doctor-content')
                </main>
            </div>
        </div>
    </body>
</html>
