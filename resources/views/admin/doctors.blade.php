@extends('admin.layout')

@php
    $pageTitle = 'Admin Doctors | CareFinder';
    $topbarSearchAction = route('admin.doctors');
    $topbarSearchValue = $search;
    $topbarSearchPlaceholder = 'Search doctors by name, specialization, hospital, location, or status';

    $docPending = (int) ($doctorStatusCounts['pending'] ?? 0);
    $docApproved = (int) ($doctorStatusCounts['approved'] ?? 0);
    $docRejected = (int) ($doctorStatusCounts['rejected'] ?? 0);
    $docTotal = max(1, $docPending + $docApproved + $docRejected);
@endphp

@section('admin-content')
    <section class="admin-modern-welcome">
        <div>
            <p class="eyebrow">Doctor Management</p>
            <h1>Add, verify, and maintain doctor profiles.</h1>
            <p>Use search and status controls to keep onboarding and approvals organized.</p>
        </div>
        <div class="admin-modern-stats">
            <article>
                <span>Total Doctors</span>
                <strong>{{ $doctorCount }}</strong>
            </article>
            <article>
                <span>Approved</span>
                <strong>{{ $docApproved }}</strong>
            </article>
            <article>
                <span>Pending</span>
                <strong>{{ $docPending }}</strong>
            </article>
        </div>
    </section>

    <section class="admin-modern-grid">
        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Doctor Status</p>
                    <h2>Approval Distribution</h2>
                </div>
            </div>
            <div class="admin-status-tracker">
                <div>
                    <div class="label-row"><span>Approved</span><strong>{{ $docApproved }}</strong></div>
                    <div class="progress approved"><span style="width: {{ (int) round(($docApproved / $docTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Pending</span><strong>{{ $docPending }}</strong></div>
                    <div class="progress pending"><span style="width: {{ (int) round(($docPending / $docTotal) * 100) }}%"></span></div>
                </div>
                <div>
                    <div class="label-row"><span>Rejected</span><strong>{{ $docRejected }}</strong></div>
                    <div class="progress rejected"><span style="width: {{ (int) round(($docRejected / $docTotal) * 100) }}%"></span></div>
                </div>
            </div>
        </article>

        <article class="admin-modern-section">
            <div class="admin-modern-section-head">
                <div>
                    <p class="eyebrow">Recent Doctors</p>
                    <h2>Latest Added Profiles</h2>
                </div>
            </div>
            <div class="admin-recent-list">
                @forelse ($recentDoctors as $doctor)
                    <div class="admin-recent-item">
                        <strong>{{ $doctor->user?->name ?? 'Doctor' }}</strong>
                        <p>{{ $doctor->specialization }} • {{ $doctor->hospital }}</p>
                        <span class="status-pill {{ $doctor->status }}">{{ ucfirst($doctor->status) }}</span>
                    </div>
                @empty
                    <p class="admin-modern-empty">No doctors found.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="admin-modern-section">
        <div class="admin-modern-section-head">
            <div>
                <p class="eyebrow">Add Doctor</p>
                <h2>Create Doctor Profile</h2>
            </div>
        </div>

        <form class="admin-modern-form" method="POST" action="{{ route('admin.doctors.store') }}">
            @csrf
            <label><span>Name</span><input name="name" value="{{ old('name') }}" required></label>
            <label><span>Email</span><input type="email" name="email" value="{{ old('email') }}" required></label>
            <label><span>Password</span><input type="password" name="password" required></label>
            <label><span>Confirm password</span><input type="password" name="password_confirmation" required></label>
            <label><span>Specialization</span><input name="specialization" value="{{ old('specialization') }}" required></label>
            <label><span>Qualification</span><input name="qualification" value="{{ old('qualification') }}" required></label>
            <label><span>Experience</span><input type="number" name="experience" min="0" max="80" value="{{ old('experience') }}" required></label>
            <label><span>Consultation fee</span><input type="number" name="consultation_fee" min="0" step="0.01" value="{{ old('consultation_fee') }}" required></label>
            <label><span>Hospital</span><input name="hospital" value="{{ old('hospital') }}" required></label>
            <label><span>Location</span><input name="location" value="{{ old('location') }}" required></label>
            <label><span>Profile image URL</span><input type="url" name="profile_image" value="{{ old('profile_image') }}"></label>
            <label>
                <span>Status</span>
                <select name="status" required>
                    <option value="approved" @selected(old('status', 'approved') === 'approved')>Approved</option>
                    <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                    <option value="rejected" @selected(old('status') === 'rejected')>Rejected</option>
                </select>
            </label>
            <label class="full"><span>About</span><textarea name="about" rows="4" required>{{ old('about') }}</textarea></label>
            <button class="admin-submit full" type="submit">Add doctor</button>
        </form>
    </section>

    <section class="admin-modern-section">
        <div class="admin-modern-section-head">
            <div>
                <p class="eyebrow">Manage Doctors</p>
                <h2>Update Doctor Status</h2>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table-modern">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Specialization</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($doctors as $doctor)
                        <tr>
                            <td><strong>{{ $doctor->user?->name }}</strong><small>{{ $doctor->hospital }}</small></td>
                            <td>{{ $doctor->specialization }}</td>
                            <td>{{ $doctor->location }}</td>
                            <td><span class="status-pill {{ $doctor->status }}">{{ ucfirst($doctor->status) }}</span></td>
                            <td>
                                <form class="inline-form" method="POST" action="{{ route('admin.doctors.status', $doctor) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status">
                                        <option value="approved" @selected($doctor->status === 'approved')>Approved</option>
                                        <option value="pending" @selected($doctor->status === 'pending')>Pending</option>
                                        <option value="rejected" @selected($doctor->status === 'rejected')>Rejected</option>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No doctors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
