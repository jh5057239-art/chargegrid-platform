<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_password_is_hashed(): void
    {
        $response = $this->post('/register', [
            'name' => 'Demo User',
            'phone' => '66900000000',
            'email' => 'demo@example.test',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/dashboard');
        $user = User::where('phone', '66900000000')->firstOrFail();
        $this->assertTrue(Hash::check('SecurePass123!', $user->password));
        $this->assertNotEmpty($user->invite_code);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_unknown_invite_code(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Demo User',
            'phone' => '66900000001',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'invite_code' => 'NOTREAL',
        ]);
        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('invite_code');
        $this->assertDatabaseMissing('users', ['phone' => '66900000001']);
    }
}
