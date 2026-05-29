<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register | CareFinder</title>
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
                    <p class="eyebrow">Patient registration</p>
                    <h1>Create your CareFinder patient account.</h1>
                    <p>Your details are stored in the database and used to open your patient dashboard after registration.</p>
                </div>

                <form class="auth-form" method="POST" action="{{ route('register') }}">
                    @csrf

                    <label>
                        <span>Name</span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        <x-input-error :messages="$errors->get('name')" class="field-error" />
                    </label>

                    <label>
                        <span>Email</span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        <x-input-error :messages="$errors->get('email')" class="field-error" />
                    </label>

                    <label>
                        <span>Password</span>
                        <input id="password" type="password" name="password" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password')" class="field-error" />
                    </label>

                    <label>
                        <span>Confirm password</span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="field-error" />
                    </label>

                    <button class="auth-submit" type="submit">Register as patient</button>
                </form>

                <p class="auth-switch">
                    Already have an account?
                    <a href="{{ route('login') }}">Login</a>
                </p>
            </section>

            <aside class="auth-info" aria-label="CareFinder registration details">
                <p class="eyebrow">What happens next</p>
                <h2>After registration, your database user role is saved as patient.</h2>
                <div class="overview-list">
                    <div>
                        <span class="list-icon">1</span>
                        <p>Your name, email, encrypted password, and patient role are stored.</p>
                    </div>
                    <div>
                        <span class="list-icon">2</span>
                        <p>Laravel logs you in immediately after account creation.</p>
                    </div>
                    <div>
                        <span class="list-icon">3</span>
                        <p>Your patient dashboard opens with database-backed care data.</p>
                    </div>
                </div>
            </aside>
        </main>
    </body>
</html>
