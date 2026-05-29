<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareFinderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_module_pages_can_be_rendered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.doctors'))->assertOk();
        $this->actingAs($admin)->get(route('admin.appointments'))->assertOk();
        $this->actingAs($admin)->get(route('admin.reviews'))->assertOk();
    }

    public function test_doctor_module_pages_can_be_rendered(): void
    {
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        Doctor::query()->create([
            'user_id' => $doctorUser->id,
            'specialization' => 'General Medicine',
            'qualification' => 'MBBS',
            'experience' => 5,
            'hospital' => 'Prime Care',
            'location' => 'Hyderabad',
            'consultation_fee' => 700,
            'profile_image' => 'https://example.com/doctor-profile.jpg',
            'about' => 'General physician.',
            'status' => 'approved',
        ]);

        $this->actingAs($doctorUser)->get(route('doctor.dashboard'))->assertOk();
        $this->actingAs($doctorUser)->get(route('doctor.appointments'))->assertOk();
        $this->actingAs($doctorUser)->get(route('doctor.reviews'))->assertOk();
        $this->actingAs($doctorUser)->get(route('doctor.patients'))->assertOk();
    }

    public function test_patient_cannot_create_duplicate_appointment_for_same_slot(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::query()->create([
            'user_id' => $doctorUser->id,
            'specialization' => 'Cardiology',
            'qualification' => 'MD',
            'experience' => 10,
            'hospital' => 'City Hospital',
            'location' => 'Bangalore',
            'consultation_fee' => 800,
            'profile_image' => 'https://example.com/doctor.jpg',
            'about' => 'Experienced cardiologist.',
            'status' => 'approved',
        ]);

        $slot = config('carefinder.appointment_slots')[0];
        $date = now()->addDay()->toDateString();

        $this->actingAs($patient)->post(route('appointments.store', $doctor), [
            'appointment_date' => $date,
            'appointment_slot' => $slot,
            'notes' => 'Mild chest pain',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($patient)->post(route('appointments.store', $doctor), [
            'appointment_date' => $date,
            'appointment_slot' => $slot,
            'notes' => 'Follow-up',
        ])->assertSessionHasErrors('appointment_slot');

        $this->assertSame(1, Appointment::count());
    }

    public function test_patient_can_review_only_after_confirmed_appointment(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::query()->create([
            'user_id' => $doctorUser->id,
            'specialization' => 'Dermatology',
            'qualification' => 'MBBS, MD',
            'experience' => 7,
            'hospital' => 'Green Clinic',
            'location' => 'Mysore',
            'consultation_fee' => 600,
            'profile_image' => 'https://example.com/doctor-2.jpg',
            'about' => 'Skin specialist.',
            'status' => 'approved',
        ]);

        $this->actingAs($patient)->post(route('reviews.store', $doctor), [
            'rating' => 5,
            'comment' => 'Excellent consultation',
        ])->assertSessionHasErrors('rating');

        Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_slot' => config('carefinder.appointment_slots')[1],
            'status' => 'confirmed',
        ]);

        $this->actingAs($patient)->post(route('reviews.store', $doctor), [
            'rating' => 5,
            'comment' => 'Excellent consultation',
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, Review::count());
    }

    public function test_patient_can_update_existing_review(): void
    {
        $patient = User::factory()->create(['role' => 'patient']);
        $doctorUser = User::factory()->create(['role' => 'doctor']);
        $doctor = Doctor::query()->create([
            'user_id' => $doctorUser->id,
            'specialization' => 'Neurology',
            'qualification' => 'MD, DM',
            'experience' => 9,
            'hospital' => 'Neuro Center',
            'location' => 'Chennai',
            'consultation_fee' => 900,
            'profile_image' => 'https://example.com/doctor-3.jpg',
            'about' => 'Neurology specialist.',
            'status' => 'approved',
        ]);

        Appointment::query()->create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->toDateString(),
            'appointment_slot' => config('carefinder.appointment_slots')[1],
            'status' => 'confirmed',
        ]);

        $this->actingAs($patient)->post(route('reviews.store', $doctor), [
            'rating' => 5,
            'comment' => 'Excellent consultation',
        ])->assertSessionHasNoErrors();

        $this->actingAs($patient)->post(route('reviews.store', $doctor), [
            'rating' => 3,
            'comment' => 'Updated feedback after follow-up',
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, Review::count());

        $this->assertDatabaseHas('reviews', [
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'rating' => 3,
            'comment' => 'Updated feedback after follow-up',
        ]);
    }
}
