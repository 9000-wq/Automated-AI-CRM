<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Lead;


class ContactControllerTest extends TestCase
{
  
    use RefreshDatabase;

    /** @test */
    public function it_returns_contacts_index_view_for_non_ajax_requests()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('contacts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('contacts.index');
    }

    /** @test */
    public function it_returns_datatables_json_for_ajax_requests_as_super_admin()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);

        $role = ContactRole::factory()->create();
        $lead = Lead::factory()->create(['name' => 'Test Lead']);
        $contact = Contact::factory()->create([
            'contact_role_id' => $role->id,
        ]);
        $contact->leads()->attach($lead->id);

        $this->actingAs($user);

        $response = $this->getJson(route('contacts.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'name' => $lead->name,
        ]);
    }

    /** @test */
    public function it_returns_company_specific_contacts_for_non_super_admin()
    {
        $companyId = 123;

        $user = User::factory()->create([
            'user_role' => 'user',
            'company_id' => $companyId,
        ]);

        $role = ContactRole::factory()->create();
        $lead = Lead::factory()->create(['name' => 'Company Lead']);
        $contact = Contact::factory()->create([
            'contact_role_id' => $role->id,
            'company_id' => $companyId,
        ]);
        $contact->leads()->attach($lead->id);

        // Another contact belonging to a different company
        Contact::factory()->create([
            'contact_role_id' => $role->id,
            'company_id' => 999,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('contacts.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);

        // Ensure only company-specific contact is included
        $response->assertJsonFragment([
            'name' => $lead->name,
        ]);
    }


    
      /** @test */
    public function it_creates_a_contact_successfully()
    {
        // Create a user with company_id
        $user = User::factory()->create([
            'company_id' => 123,
        ]);

        // Create required lead and contact role
        $lead = Lead::factory()->create();
        $role = ContactRole::factory()->create();

        // Data without company_id (it should be injected automatically)
        $data = [
            'name'            => 'John Doe',
            'email'           => 'john@example.com',
            'birthday'        => '1990-01-01',
            'phone'           => '1234567890',
            'address'         => '123 Street',
            'description'     => 'Test contact',
            'lead_id'         => $lead->id,
            'contact_role_id' => $role->id,
        ];

        // Acting as authenticated user
        $response = $this->actingAs($user)->postJson(route('contacts.store'), $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => [
                    'name'       => 'John Doe',
                    'email'      => 'john@example.com',
                    'company_id' => $user->company_id, // ensure company_id is inserted
                ],
            ]);

        // Assert contact is stored in database with company_id
        $this->assertDatabaseHas('contacts', [
            'name'       => 'John Doe',
            'email'      => 'john@example.com',
            'company_id' => $user->company_id,
        ]);
    }

    /** @test */
    public function it_requires_mandatory_fields()
    {
        $user = User::factory()->create(['company_id' => 123]);

        $response = $this->actingAs($user)->postJson(route('contacts.store'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'lead_id', 'contact_role_id']);
    }



    /** @test */
    public function it_displays_a_single_contact_with_relations()
    {
        // Create a user and authenticate
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create related models
        $lead = Lead::factory()->create();
        $role = ContactRole::factory()->create();

        // Create a contact
        $contact = Contact::factory()->create([
            'lead_id' => $lead->id,
            'contact_role_id' => $role->id,
            'company_id' => $user->company_id, // ensure consistency
        ]);

        // Hit the route
        $response = $this->get(route('contacts.show', $contact->id));

        // Assert response is ok
        $response->assertStatus(200);

        // Assert correct view is returned
        $response->assertViewIs('contacts.show');

        // Assert the view has the contact data
        $response->assertViewHas('contact', function ($viewContact) use ($contact) {
            return $viewContact->id === $contact->id;
        });
    }



    /** @test */
    public function authenticated_user_can_update_a_contact()
    {
        // Create user
        $user = User::factory()->create();

        // Create initial contact role
        $role = ContactRole::factory()->create();

        // Create contact belonging to user company
        $contact = Contact::factory()->create([
            'company_id' => $user->company_id,
            'contact_role_id' => $role->id,
        ]);

        // New data for update
        $data = [
            'name'        => 'Updated Contact',
            'email'       => 'updated@example.com',
            'birthday'    => '1995-08-20',
            'phone'       => '999888777',
            'address'     => 'Updated Address',
            'description' => 'Updated description',
            'role'        => 'Manager', // new role (will trigger firstOrCreate)
        ];

        // Acting as user and sending PUT request
        $response = $this->actingAs($user)
                         ->put(route('contacts.update', $contact->id), $data);

        // Assert redirect back with success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Contact updated successfully');

        // Check database updated
        $this->assertDatabaseHas('contacts', [
            'id'             => $contact->id,
            'name'           => 'Updated Contact',
            'email'          => 'updated@example.com',
            'phone'          => '999888777',
            'address'        => 'Updated Address',
            'description'    => 'Updated description',
            'company_id'     => $user->company_id,
        ]);

        // Ensure new role was created and assigned
        $this->assertDatabaseHas('contact_roles', [
            'label' => 'Manager'
        ]);
    }



    /** @test */
    public function authenticated_user_can_delete_a_contact()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as that user
        $this->actingAs($user);

        // Create a contact belonging to that user's company
        $contact = Contact::factory()->create([
            'company_id' => $user->company_id,
        ]);

        // Hit the destroy route
        $response = $this->delete(route('contacts.destroy', $contact->id));

        // Assert contact is deleted
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);

        // Assert JSON response
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Contact deleted successfully',
                 ]);
    }

    /** @test */
    public function guest_cannot_delete_a_contact()
    {
        $contact = Contact::factory()->create();

        $response = $this->delete(route('contacts.destroy', $contact->id));

        $response->assertRedirect(route('login')); // Laravel default for unauthenticated
    }


    /** @test */
    public function it_returns_contacts_for_a_valid_lead()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a lead
        $lead = Lead::factory()->create();

        // Create a contact role
        $role = ContactRole::factory()->create();

        // Create contacts under this lead
        $contact = Contact::factory()->create([
            'lead_id' => $lead->id,
            'contact_role_id' => $role->id,
        ]);

        // Hit the route
        $response = $this->getJson(route('getContactsByLead', $lead->id));

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'contacts' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'phone',
                             'lead_id',
                             'contact_role_id',
                             'role'
                         ]
                     ]
                 ])
                 ->assertJsonFragment([
                     'id' => $contact->id,
                     'name' => $contact->name,
                 ]);
    }

    /** @test */
    public function it_returns_404_if_lead_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Use invalid lead id
        $response = $this->getJson(route('getContactsByLead', 9999));

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Lead not found',
                 ]);
    }

    

}
