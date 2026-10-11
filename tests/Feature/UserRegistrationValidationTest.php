<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('Create an Account');
        $response->assertSee('Register As');
        $response->assertSee('User');
        $response->assertSee('Student');
        $response->assertSee('I agree to the');
        $response->assertSee('Terms of Service');
    }

    public function test_registration_requires_terms_agreed(): void
    {
        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'John Doe',
            'email' => 'john.doe@gmail.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'user',
            // terms_agreed missing
        ]);

        $response->assertSessionHasErrors('terms_agreed');
        $this->assertDatabaseMissing('users', [
            'email' => 'john.doe@gmail.com',
        ]);
    }

    public function test_user_can_register_with_terms_agreed(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Citizen',
            'email' => 'jane.citizen@gmail.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'user',
            'terms_agreed' => '1',
        ]);

        $response->assertRedirect(route('website.home'));
        $this->assertDatabaseHas('users', [
            'email' => 'jane.citizen@gmail.com',
            'role' => 'user',
        ]);
    }

    public function test_student_registration_requires_student_id(): void
    {
        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Student Sam',
            'email' => 'student.sam@gmail.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'student',
            'terms_agreed' => '1',
            // student_id missing
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertDatabaseMissing('users', [
            'email' => 'student.sam@gmail.com',
        ]);
    }

    public function test_student_can_register_with_student_id_and_terms(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Student Alex',
            'email' => 'student.alex@gmail.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'student',
            'student_id' => '2026-0099',
            'section' => 'BSIT 4A',
            'terms_agreed' => '1',
        ]);

        $response->assertRedirect(route('website.home'));
        $this->assertDatabaseHas('users', [
            'email' => 'student.alex@gmail.com',
            'role' => 'student',
            'student_id' => '2026-0099',
            'section' => 'BSIT 4A',
        ]);
    }
}
