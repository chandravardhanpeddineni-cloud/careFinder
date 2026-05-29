<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $pageTitle ?? 'Admin Dashboard | CareFinder' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('admin-modern.css') }}">
    </head>
    <body>
        @php
            $showSearch = isset($topbarSearchAction) && $topbarSearchAction !== '';
            $topbarSearchInputName = $topbarSearchInputName ?? 'q';
            $topbarSearchValue = $topbarSearchValue ?? '';
            $topbarSearchPlaceholder = $topbarSearchPlaceholder ?? 'Search';
        @endphp

        <div class="admin-modern-page">
            <aside class="admin-modern-sidebar">
                <a class="admin-modern-brand" href="{{ route('admin.dashboard') }}">
                    <span class="admin-modern-brand-mark">CF</span>
                    <div>
                        <strong>CareFinder</strong>
                        <small>Admin Console</small>
                    </div>
                </a>

                <nav class="admin-modern-nav">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.doctors') }}" class="{{ request()->routeIs('admin.doctors') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 11h-6V5h-2v6H5v2h6v6h2v-6h6z"/></svg>
                        <span>Doctors</span>
                    </a>
                    <a href="{{ route('admin.appointments') }}" class="{{ request()->routeIs('admin.appointments') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm0 15H5V10h14v9Z"/></svg>
                        <span>Appointments</span>
                    </a>
                    <a href="{{ route('admin.reviews') }}" class="{{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17.3 18.2 21l-1.7-7.1L22 9.2l-7.2-.6L12 2 9.2 8.6 2 9.2l5.5 4.7L5.8 21 12 17.3Z"/></svg>
                        <span>Reviews</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m19.14 12.94.86-1.49-1.5-2.6-1.73.18a5.9 5.9 0 0 0-1.03-.6L15.5 6h-3l-.24 2.43c-.36.15-.7.35-1.02.6l-1.74-.18-1.5 2.6.86 1.49c-.03.2-.06.4-.06.61 0 .21.03.41.06.61l-.86 1.49 1.5 2.6 1.74-.18c.32.25.66.45 1.02.6L12.5 22h3l.24-2.43c.36-.15.7-.35 1.03-.6l1.73.18 1.5-2.6-.86-1.49c.03-.2.06-.4.06-.61 0-.21-.03-.41-.06-.61ZM14 16a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z"/></svg>
                        <span>Settings</span>
                    </a>
                </nav>
            </aside>

            <div class="admin-modern-main">
                <header class="admin-modern-topbar">
                    <a class="admin-mobile-brand" href="{{ route('admin.dashboard') }}">CareFinder</a>

                    @if ($showSearch)
                        <form method="GET" action="{{ $topbarSearchAction }}" class="admin-modern-search">
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
                        <div class="admin-modern-title">
                            <p class="eyebrow">{{ $pageEyebrow ?? 'Admin Dashboard' }}</p>
                            <h1>{{ $pageHeading ?? 'Platform Operations' }}</h1>
                        </div>
                    @endif

                    <div class="admin-modern-top-actions">
                        <div class="admin-modern-profile">
                            <span class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ Auth::user()->name }}</strong>
                                <small>System Admin</small>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="admin-logout-btn" type="submit">Logout</button>
                        </form>
                    </div>
                </header>

                <main class="admin-modern-content">
                    @if (session('status'))
                        <div class="admin-modern-alert">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="admin-modern-alert error">
                            <strong>Please fix the highlighted fields.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('admin-content')
                </main>
            </div>
        </div>
    </body>
</html>
