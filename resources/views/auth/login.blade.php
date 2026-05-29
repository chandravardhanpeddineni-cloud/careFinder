<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | CareFinder</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <main class="auth-page">
            <section class="auth-panel">
                <a class="brand public-brand" href="{{ url('/') }}">
                    <span class="brand-mark">CF</span>
                    <span>
                        <strong>CareFinder</strong>
                        <small>Patient portal</small>
                    </span>
                </a>

                <div class="auth-heading">
                    <p class="eyebrow">Welcome back</p>
                    <h1>Login to your CareFinder account.</h1>
                    <p>Your dashboard uses your saved database account to show patient details and care activity.</p>
                </div>

                <x-auth-session-status class="auth-status" :status="session('status')" />

                <form class="auth-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <label>
                        <span>Email</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="field-error" />
                    </label>

                    <label>
                        <span>Role</span>
                        <select id="role" name="role" required autocomplete="organization-title">
                            <option value="" @selected(old('role') === null)>Select role</option>
                            <option value="patient" @selected(old('role', 'patient') === 'patient')>Patient</option>
                            <option value="doctor" @selected(old('role') === 'doctor')>Doctor</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="field-error" />
                    </label>

                    <label>
                        <span>Password</span>
                        <input id="password" type="password" name="password" required autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="field-error" />
                    </label>

                    <div class="auth-options">
                        <label class="remember-option" for="remember_me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        @endif
                    </div>

                    <button class="auth-submit" type="submit">Login</button>
                </form>

                <p class="auth-switch">
                    New to CareFinder?
                    <a href="{{ route('register') }}">Create patient account</a>
                </p>
            </section>

            <aside class="auth-info" aria-label="CareFinder login details">
                <p class="eyebrow">Database powered</p>
                <h2>Login checks your email and password from the users table.</h2>
                <div class="overview-list">
                    <div>
                        <span class="list-icon">1</span>
                        <p>Registered patient accounts are saved in MySQL.</p>
                    </div>
                    <div>
                        <span class="list-icon">2</span>
                        <p>After login, Laravel fetches your role and opens the right dashboard.</p>
                    </div>
                    <div>
                        <span class="list-icon">3</span>
                        <p>Patient dashboard content is loaded from appointments and doctors.</p>
                    </div>
                </div>
            </aside>
        </main>
    </body>
</html>
