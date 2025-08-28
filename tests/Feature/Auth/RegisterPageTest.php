<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;


class RegisterPageTest extends TestCase
{
  
    use RefreshDatabase;

    /** @test */
    public function guests_can_view_the_register_page()
    {
        $response = $this->get(route('register'));

        $response->assertOk();                    // HTTP 200
        $response->assertViewIs('auth.register'); // Correct blade view

        // Optional: assert a piece of text that appears on the page
        $response->assertSee('Register', false);
    }

    /** @test */
    public function authenticated_users_are_redirected_from_register_page()
    {
        // If you’re using Breeze/Jetstream, guest middleware usually redirects to 'dashboard'
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('register'));

        // Adjust target if your app uses a different post-login page
        $response->assertRedirect(route('dashboard')); // or ->assertRedirect('/dashboard')
    }


    /** @test */
    public function a_user_can_register_with_company_as_service()
    {
        $response = $this->post(route('register'), [
            'companyName' => 'TechSoft',
            'company_email' => 'company@example.com',
            'companyAddress' => '123 Street',
            'country' => 'USA',
            'companyDescription' => 'We provide IT services',
            'businessType' => 'service',
            'service_knowledge' => 'Cloud, Web Apps',
            'priceGuidelines' => 'Affordable pricing',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // Assert response
        $response->assertStatus(200);
        $response->assertJson(['Success' => 'User Registered Successfully.']);

        // Assert database contains company
        $this->assertDatabaseHas('companies', [
            'company_email' => 'company@example.com',
            'business_type' => 'service',
        ]);

        // Assert database contains user
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    }

    /** @test */
    public function registration_requires_mandatory_fields()
    {
        $response = $this->post(route('register'), []); // send empty payload

        $response->assertSessionHasErrors([
            'companyName',
            'company_email',
            'firstname',
            'lastname',
            'email',
            'password',
        ]);
    }

    

}
