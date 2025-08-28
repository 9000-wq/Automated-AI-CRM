<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Account;
use App\Models\User;
use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Lead;
use App\Models\Company;


class AccountControllerTest extends TestCase
{
  
    use RefreshDatabase;

    /** @test */
    public function index_returns_all_accounts_as_json()
    {
        // Arrange: create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create some accounts in the database
        $accounts = Account::factory()->count(3)->create([
            'company_id' => $user->company_id, // make sure accounts belong to the same company
        ]);

        // Act: call the index route
        $response = $this->getJson(route('accounts.index'));

        // Assert: response is OK
        $response->assertStatus(200);

        // Assert: JSON contains the accounts
        $response->assertJsonCount(3);

        // Assert: JSON structure matches Account model fields
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'created_at',
                'updated_at',
            ],
        ]);

        // Optional: check that the data matches what was created
        $this->assertEquals($accounts->pluck('id')->toArray(), 
                            collect($response->json())->pluck('id')->toArray());
    }



    /** @test */
    public function authenticated_user_can_store_account_with_contacts()
    {
        // Arrange: create a user with a company
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Test Company',
            'industry' => 'IT',
            'email' => 'testcompany@example.com',
            'phone' => '1234567890',
            'website' => 'https://example.com',
            'address' => '123 Street',
            'city' => 'Cityname',
            'country' => 'Countryname',
            'status' => 'active',
            'contacts' => [
                [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '9876543210',
                    'birthday' => '1990-01-01',
                    'address' => '456 Street',
                    'description' => 'Manager',
                    'contact_role' => 'Manager',
                ],
                [
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'phone' => '1231231234',
                    'contact_role' => 'Assistant',
                ],
            ],
        ];

        // Act: call the store route
        $response = $this->post(route('accounts.store'), $payload);

        // Assert: redirected to accounts/home page with success message
        $response->assertRedirect(route('home.account'));
        $response->assertSessionHas('success', 'Account created successfully!');

        // Assert: account is stored in DB
        $this->assertDatabaseHas('accounts', [
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            'company_id' => $user->company_id,
        ]);

        $account = Account::where('email', 'testcompany@example.com')->first();

        // Assert: contacts are stored in DB
        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'account_id' => $account->id,
            'company_id' => $user->company_id,
        ]);

        $this->assertDatabaseHas('contacts', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'account_id' => $account->id,
            'company_id' => $user->company_id,
        ]);

        // Assert: contact roles created
        $this->assertDatabaseHas('contact_roles', [
            'label' => 'Manager',
        ]);

        $this->assertDatabaseHas('contact_roles', [
            'label' => 'Assistant',
        ]);
    }

    /** @test */
    public function guest_cannot_store_account()
    {
        $payload = [
            'name' => 'Test Company',
            'email' => 'testcompany@example.com',
            'phone' => '1234567890',
            'status' => 'active',
        ];

        $response = $this->post(route('accounts.store'), $payload);

        // Should redirect to login
        $response->assertRedirect(route('login'));
    }


        /** @test */
    public function it_returns_account_as_json()
    {
        // Arrange: create a user and an account
        $user = User::factory()->create();
        $account = Account::factory()->create();

        // Act: act as the user and hit the show route
        $response = $this->actingAs($user)->getJson(route('accounts.show', $account->id));

        // Assert: response status and structure
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $account->id,
                     'name' => $account->name,
                     'email' => $account->email,
                     // add more fields if needed
                 ]);
    }

    /** @test */
    public function it_returns_404_if_account_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('accounts.show', 9999)); // non-existent ID

        $response->assertStatus(404);
    }



    /** @test */
    public function authenticated_user_can_access_account_edit_page()
    {
        // Arrange: create a user, account, and related contacts
        $user = User::factory()->create();
        $account = Account::factory()->create();

        $role = ContactRole::factory()->create(['label' => 'Manager']);
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
            'contact_role_id' => $role->id,
        ]);

        // Act: act as the user and hit the edit route
        $response = $this->actingAs($user)->get(route('accounts.edit', $account->id));

        // Assert: check view and data
        $response->assertStatus(200);
        $response->assertViewIs('editaccount');
        $response->assertViewHas('account', function ($viewAccount) use ($account) {
            return $viewAccount->id === $account->id;
        });

        // Optional: ensure contact is loaded with role
        $this->assertTrue($response->viewData('account')->contacts->first()->ContactRole->label === 'Manager');
    }

    /** @test */
    public function guest_user_cannot_access_account_edit_page()
    {
        $account = Account::factory()->create();

        $response = $this->get(route('accounts.edit', $account->id));

        $response->assertRedirect(route('login')); // assuming 'auth' middleware
    }


        /** @test */
    public function authenticated_user_can_update_account_and_contacts()
    {
        // Arrange: create user, account, existing contact
        $user = User::factory()->create();
        $account = Account::factory()->create(['company_id' => $user->company_id]);

        $role = ContactRole::factory()->create(['label' => 'Manager']);
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
            'contact_role_id' => $role->id,
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        // Payload: update account + existing contact + new contact
        $payload = [
            'name' => 'Updated Account',
            'email' => 'updated@example.com',
            'status' => 'active',
            'contacts' => [
                [
                    'contact_id' => $contact->id,
                    'name' => 'Updated Contact',
                    'email' => 'contact1updated@example.com',
                    'phone' => '1234567890',
                    'contact_role' => 'Manager',
                ],
                [
                    'name' => 'New Contact',
                    'email' => 'newcontact@example.com',
                    'phone' => '0987654321',
                    'contact_role' => 'Assistant',
                ]
            ]
        ];

        // Act
        $response = $this->put(route('accounts.update', $account->id), $payload);

        // Assert redirect and session message
        $response->assertRedirect(route('home.account'));
        $response->assertSessionHas('success', 'Account updated with contacts');

        // Assert account updated
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'Updated Account',
            'email' => 'updated@example.com',
        ]);

        // Assert existing contact updated
        $this->assertDatabaseHas('contacts', [
            'id' => $contact->id,
            'name' => 'Updated Contact',
            'email' => 'contact1updated@example.com',
            'phone' => '1234567890',
        ]);

        // Assert new contact created
        $this->assertDatabaseHas('contacts', [
            'name' => 'New Contact',
            'email' => 'newcontact@example.com',
            'phone' => '0987654321',
        ]);

        // Assert contact roles created or reused
        $this->assertDatabaseHas('contact_roles', ['label' => 'Assistant']);
    }

    /** @test */
    public function guest_user_cannot_update_account()
    {
        $account = Account::factory()->create();

        $response = $this->put(route('accounts.update', $account->id), [
            'name' => 'Test',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('login'));
    }


        /** @test */
    public function authenticated_user_can_delete_account_and_contacts()
    {
        // Arrange: create user, account, and contacts
        $user = User::factory()->create();
        $account = Account::factory()->create(['company_id' => $user->company_id]);
        $contacts = Contact::factory()->count(2)->create([
            'account_id' => $account->id,
            'company_id' => $user->company_id,
        ]);

        $this->actingAs($user);

        // Act: delete the account
        $response = $this->deleteJson(route('accounts.destroy', $account->id));

        // Assert: account deleted
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);

        // Assert: contacts deleted
        foreach ($contacts as $contact) {
            $this->assertDatabaseMissing('contacts', [
                'id' => $contact->id,
            ]);
        }

        // Assert: correct JSON response
        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Account and associated contacts deleted successfully',
                 ]);
    }

    /** @test */
    public function guest_user_cannot_delete_account()
    {
        $account = Account::factory()->create();

        $response = $this->deleteJson(route('accounts.destroy', $account->id));

        $response->assertStatus(401); // Unauthorized
        $this->assertDatabaseHas('accounts', ['id' => $account->id]);
    }



    /** @test */
    public function it_returns_contacts_for_a_given_account()
    {
        // Arrange: create a user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Arrange: create an account for the user's company
        $account = Account::factory()->create([
            'company_id' => $user->company_id,
        ]);

        // Arrange: create some contacts for the account
        $contacts = Contact::factory()->count(3)->create([
            'account_id' => $account->id,
            'company_id' => $user->company_id,
        ]);

        // Act: call the getContacts route
        $response = $this->getJson(route('accounts.getContacts', $account->id));

        // Assert: response is OK
        $response->assertStatus(200);

        // Assert: JSON contains all contacts
        $response->assertJsonCount(3);

        // Assert: JSON structure matches Contact fields
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'email',
                'phone',
                'birthday',
                'address',
                'description',
                'contact_role_id',
                'account_id',
                'company_id',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    /** @test */
    public function it_returns_404_if_account_not_found2()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('accounts.getContacts', 99999)); // non-existing account

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Account not found',
                ]);
    }


 
    /** @test */
    public function it_returns_leads_for_a_given_account()
    {

        // Create a fake user
        $user = \App\Models\User::factory()->create();

        // Act as this user
        $this->actingAs($user);

        // Arrange: create company and account
        $company = Company::factory()->create();
        $account = Account::factory()->create(['company_id' => $company->id]);

        // Create 3 leads for this account
        $leads = Lead::factory()
            ->count(3)
            ->create([
                'account_id' => $account->id,
                'company_id' => $company->id,
                'assigned_to' => User::factory(), // assigned user
            ]);

        // Act: call the route
        $response = $this->getJson(route('accounts.getLeads', $account->id));

        // Assert: response is OK
        $response->assertStatus(200);

        // Assert: JSON contains the 3 leads
        $response->assertJsonCount(3);

        // Optional: ensure returned leads match the created ones
        $this->assertEquals(
            $leads->pluck('id')->sort()->values()->toArray(),
            collect($response->json())->pluck('id')->sort()->values()->toArray()
        );
    }


    /** @test */
    public function it_returns_404_if_account_not_found3()
    {
        // Create a fake user
        $user = \App\Models\User::factory()->create();

        // Act as this user
        $this->actingAs($user);

        // Call the route with a non-existent account ID
        $response = $this->getJson(route('accounts.getLeads', 999));

        $response->assertStatus(404);
        $response->assertJson(['message' => 'Account not found']);
    }



   
    /** @test */
    public function it_displays_the_create_account_view()
    {
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get(route('account'));

        $response->assertStatus(200);

        // Check some unique text in your view
        $response->assertSee('Create new Account'); 
    }




}
