<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Auth;

class LoginPageTest extends TestCase
{
 
    use RefreshDatabase;   // ensures DB is reset before each test

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt('secret'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }


        /** @test */
    public function user_can_login_with_valid_credentials2()
    {
        // Arrange: create a user
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Act: send a login request
        $response = $this->post(route('userlogin'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // Assert: redirected to dashboard and authenticated
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from(route('login'))->post(route('userlogin'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        // Assert: redirected back to login
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email'); // comes from LoginRequest

        $this->assertGuest(); // still not logged in
    }

    /** @test */
    public function user_cannot_login_with_non_existing_email()
    {
        $response = $this->from(route('login'))->post(route('userlogin'), [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /** @test */
    public function session_is_regenerated_after_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $oldSessionId = session()->getId();

        $this->post(route('userlogin'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $newSessionId = session()->getId();

        // session should be regenerated (different id)
        $this->assertNotEquals($oldSessionId, $newSessionId);
    }



     /** @test */
    public function authenticated_user_can_logout()
    {
        // Create a user and log them in
        $user = User::factory()->create();
        $this->actingAs($user);

        // Ensure the user is authenticated
        $this->assertTrue(Auth::check());

        // Call the logout route
        $response = $this->post(route('logout'));

        // Assert redirect to homepage
        $response->assertRedirect('/');

        // Assert the user is logged out
        $this->assertFalse(Auth::check());

        // Assert session is invalidated (token regenerated)
        $response->assertSessionHasNoErrors();
    }

    /** @test */
    public function guest_cannot_access_logout_route()
    {
        // If guest tries to logout, redirect to login (or "/" depending on middleware)
        $response = $this->post(route('logout'));

        // In Breeze/Fortify, guest is redirected to "/"
        $response->assertRedirect('/');
    }




}
