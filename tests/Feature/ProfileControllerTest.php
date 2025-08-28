<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ProfileControllerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_access_profile_edit_page()
    {

        // Create a user
        $user = User::factory()->create();

        // Act as the authenticated user
        $response = $this->actingAs($user)->get(route('profile.edit'));

        // Assert: authenticated user can access
        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertViewHas('user', $user);
        $response->assertSee($user->name);

    }

    /** @test */
    public function guest_user_cannot_access_profile_edit_page()
    {
        $response = $this->get(route('profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function authenticated_user_can_update_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'old@example.com', // same email, so email_verified_at should not change
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'old@example.com',
        ]);
    }

    /** @test */
    public function updating_email_resets_email_verification()
    {
        $user = User::factory()->create([
            'email' => 'old@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@example.com',
            'email_verified_at' => null, // should reset
        ]);
    }

    /** @test */
    public function guest_cannot_update_profile()
    {
        $response = $this->patch(route('profile.update'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }



       /** @test */
    public function user_can_delete_their_profile_with_correct_password()
    {
        // Arrange: create a user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Act: attempt profile deletion
        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        // Assert: user is deleted and redirected
        $response->assertRedirect('/');
        $this->assertGuest(); // user should be logged out
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_profile_with_wrong_password()
    {
        // Arrange
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Act: try deleting with wrong password
        $response = $this->actingAs($user)->from(route('profile.edit'))
            ->delete(route('profile.destroy'), [
                'password' => 'wrongpassword',
            ]);

        // Assert: stays on profile page, validation error
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasErrors('password', null, 'userDeletion');

        // Ensure user still exists
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function guest_cannot_delete_profile()
    {
        // Act
        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect(route('login'));
    }

    

}
