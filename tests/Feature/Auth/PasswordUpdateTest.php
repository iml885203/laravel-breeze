<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->updatePassword($user, [
            'current_password' => 'password',
            'password' => 'new-Password1',
            'password_confirmation' => 'new-Password1',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-Password1', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this->updatePassword($user, [
            'current_password' => 'wrong-password',
            'password' => 'new-Password1',
            'password_confirmation' => 'new-Password1',
        ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }

    public function test_new_password_cannot_be_same_as_last_three_passwords(): void
    {
        $user = User::factory()->create();

        // Create password histories
        $response = $this->updatePassword($user, [
            'current_password' => 'password',
            'password' => 'new-Password1',
            'password_confirmation' => 'new-Password1',
        ]);
        $response->assertSessionHasNoErrors();

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password1',
            'password' => 'new-Password2',
            'password_confirmation' => 'new-Password2',
        ]);
        $response->assertSessionHasNoErrors();

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password2',
            'password' => 'new-Password3',
            'password_confirmation' => 'new-Password3',
        ]);
        $response->assertSessionHasNoErrors();

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password3',
            'password' => 'new-Password4',
            'password_confirmation' => 'new-Password4',
        ]);
        $response->assertSessionHasNoErrors();

        // Try to use a password that was used in the last three passwords
        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password4',
            'password' => 'new-Password4',
            'password_confirmation' => 'new-Password4',
        ]);
        $response->assertSessionHasErrorsIn('updatePassword', 'password');

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password4',
            'password' => 'new-Password3',
            'password_confirmation' => 'new-Password3',
        ]);
        $response->assertSessionHasErrorsIn('updatePassword', 'password');

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password4',
            'password' => 'new-Password2',
            'password_confirmation' => 'new-Password2',
        ]);
        $response->assertSessionHasErrorsIn('updatePassword', 'password');

        // Try to use a password that was not used in the last three passwords.
        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password4',
            'password' => 'new-Password1',
            'password_confirmation' => 'new-Password1',
        ]);
        $response->assertSessionHasNoErrors();

        $response = $this->updatePassword($user, [
            'current_password' => 'new-Password1',
            'password' => 'new-Password5',
            'password_confirmation' => 'new-Password5',
        ]);
        $response->assertSessionHasNoErrors();
    }

    public function test_new_password_must_be_confirmed_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this->updatePassword($user, [
            'current_password' => 'password',
            'password' => 'new-Password1',
            'password_confirmation' => 'wrong-Password1',
        ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'password')
            ->assertRedirect('/profile');
    }

    private function updatePassword(User $user, array $data): \Illuminate\Testing\TestResponse
    {
        return $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', $data);
    }
}
