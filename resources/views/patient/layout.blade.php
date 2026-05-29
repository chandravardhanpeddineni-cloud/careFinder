<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ?? 'Patient Dashboard | CareFinder' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('patient-modern.css') }}">
    </head>
    <body>
        @php
            $showSearch = isset($topbarSearchAction) && $topbarSearchAction !== '';
            $topbarSearchInputName = $topbarSearchInputName ?? 'q';
            $topbarSearchValue = $topbarSearchValue ?? '';
            $topbarSearchPlaceholder = $topbarSearchPlaceholder ?? 'Search';
        @endphp

        <div class="patient-modern-page">
            <aside class="patient-modern-sidebar">
                <a class="patient-modern-brand" href="{{ route('patient.dashboard') }}">
                    <span class="patient-modern-brand-mark">CF</span>
                    <div>
                        <strong>CareFinder</strong>
                        <small>Patient Portal</small>
                    </div>
                </a>

                <nav class="patient-modern-nav">
                    <a href="{{ route('patient.dashboard') }}" class="{{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('patient.doctors') }}" class="{{ request()->routeIs('patient.doctors', 'doctors.show') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 14h-.79l-.28-.27a6.5 6.5 0 1 0-.7.7l.27.28v.79L20 21.5 21.5 20l-6-6Zm-6 0a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9Z"/></svg>
                        <span>Find Doctors</span>
                    </a>
                    <a href="{{ route('patient.appointments') }}" class="{{ request()->routeIs('patient.appointments') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 15H5V10h14v9Zm0-11H5V6h14v2Z"/></svg>
                        <span>Appointments</span>
                    </a>
                    <a href="{{ route('patient.reviews') }}" class="{{ request()->routeIs('patient.reviews') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.3 18.2 21l-1.7-7.1L22 9.2l-7.2-.6L12 2 9.2 8.6 2 9.2l5.5 4.7L5.8 21 12 17.3Z"/></svg>
                        <span>Reviews</span>
                    </a>
                    <a href="{{ route('patient.prescriptions') }}" class="{{ request()->routeIs('patient.prescriptions') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Zm1 7V3.5L19.5 9H15Zm-6 4h6v2H9v-2Zm0 4h6v2H9v-2Zm0-8h2v2H9V9Z"/></svg>
                        <span>Prescriptions</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m19.14 12.94.86-1.49-1.5-2.6-1.73.18a5.9 5.9 0 0 0-1.03-.6L15.5 6h-3l-.24 2.43c-.36.15-.7.35-1.02.6l-1.74-.18-1.5 2.6.86 1.49c-.03.2-.06.4-.06.61 0 .21.03.41.06.61l-.86 1.49 1.5 2.6 1.74-.18c.32.25.66.45 1.02.6L12.5 22h3l.24-2.43c.36-.15.7-.35 1.03-.6l1.73.18 1.5-2.6-.86-1.49c.03-.2.06-.4.06-.61 0-.21-.03-.41-.06-.61ZM14 16a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z"/></svg>
                        <span>Settings</span>
                    </a>
                </nav>
            </aside>

            <div class="patient-modern-main">
                <header class="patient-modern-topbar">
                    <a class="patient-mobile-brand" href="{{ route('patient.dashboard') }}">CareFinder</a>

                    @if ($showSearch)
                        <form method="GET" action="{{ $topbarSearchAction }}" class="patient-modern-search">
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
                        <div class="patient-topbar-title">
                            <p class="eyebrow">{{ $pageEyebrow ?? 'Patient Workspace' }}</p>
                            <h1>{{ $pageHeading ?? 'CareFinder Dashboard' }}</h1>
                        </div>
                    @endif

                    <div class="patient-modern-top-actions">
                        <div class="patient-modern-profile">
                            <span class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ Auth::user()->name }}</strong>
                                <small>{{ Auth::user()->email }}</small>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="patient-logout-btn" type="submit">Logout</button>
                        </form>
                    </div>
                </header>

                <main class="patient-modern-content">
                    @if (session('status'))
                        <div class="patient-modern-alert">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="patient-modern-alert patient-modern-alert-error">
                            <strong>Please fix the highlighted fields.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('patient-content')
                </main>
            </div>
        </div>
    </body>
</html>
