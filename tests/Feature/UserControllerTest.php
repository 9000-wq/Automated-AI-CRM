<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Hash;



class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function it_returns_users_index_view_for_normal_request()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('users.index');
    }

    /** @test */
    public function super_admin_can_fetch_all_users_with_ajax()
    {
        $superAdmin = User::factory()->create([
            'user_role' => 'super admin',
        ]);

        $users = User::factory(3)->create();

        $response = $this->actingAs($superAdmin)
            ->getJson(route('users.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $users->first()->id,
        ]);
    }

    /** @test */
    public function normal_user_only_fetches_company_specific_users_with_ajax()
    {
        $companyUser = User::factory()->create([
            'user_role' => 'user',
            'company_id' => 1,
        ]);

        $sameCompanyUsers = User::factory(2)->create(['company_id' => 1]);
        $otherCompanyUsers = User::factory(2)->create(['company_id' => 2]);

        $response = $this->actingAs($companyUser)
            ->getJson(route('users.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

        // Should contain same company user
        $response->assertJsonFragment([
            'id' => $sameCompanyUsers->first()->id,
        ]);

        // Should NOT contain other company users
        $response->assertJsonMissing([
            'id' => $otherCompanyUsers->first()->id,
        ]);
    }


    /** @test */
    public function super_admin_can_access_create_users_and_see_all_companies()
    {
        // Arrange
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $superAdmin = User::factory()->create([
            'user_role' => 'super admin',
            'company_id' => $company1->id,
        ]);

        // Act
        $response = $this->actingAs($superAdmin)->get(route('users.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('users.create');
        $response->assertViewHas('companies', function ($companies) use ($company1, $company2) {
            return $companies->contains('id', $company1->id) &&
                   $companies->contains('id', $company2->id);
        });
    }

    /** @test */
    public function normal_user_can_access_create_users_and_see_only_his_company()
    {
        // Arrange
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $normalUser = User::factory()->create([
            'user_role' => 'user',
            'company_id' => $company1->id,
        ]);

        // Act
        $response = $this->actingAs($normalUser)->get(route('users.create'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('users.create');
        $response->assertViewHas('companies', function ($companies) use ($company1, $company2) {
            return $companies->contains('id', $company1->id) &&
                   !$companies->contains('id', $company2->id);
        });
    }


    
    /** @test */
    public function it_creates_a_new_user_with_valid_data()
    {
        $this->withoutExceptionHandling();

        // Arrange: create a super admin user to act as
        $admin = User::factory()->create([
            'user_role' => 'super admin',
        ]);

        $company = Company::factory()->create();

        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'user_role'  => 'admin',
            'company'    => $company->id,
            'password'   => 'secret123',
        ];

        // Act: act as admin and post
        $response = $this->actingAs($admin)->post(route('users.store'), $payload);

        // Assert
        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('success', 'User created successfully.');

        $this->assertDatabaseHas('users', [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'company_id' => $company->id,
            'user_role'  => 'admin',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    /** @test */
    public function it_requires_all_fields_to_store_user()
    {
        
        // Arrange → create a user
        $user = \App\Models\User::factory()->create();

        // Act → send request as authenticated user
        $response = $this->actingAs($user)->postJson(route('users.store'), []);

        // Assert → should fail validation (422)
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'first_name',
            'last_name',
            'email',
            'user_role',
            'company',
            'password',
        ]);

    }

    /** @test */
    public function it_requires_unique_email_for_new_user()
    {
        // Create an authenticated user
        $user = User::factory()->create();

        // Act as this user
        $this->actingAs($user);

        // Insert an existing user with a duplicate email
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $payload = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'duplicate@example.com', // same email
            'user_role'  => 'admin',
            'company'    => 'SomeCompany',
            'password'   => 'secret123',
        ];

        $response = $this->postJson(route('users.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);

    }



     /** @test */
    public function super_admin_can_access_edit_page_and_see_all_companies()
    {
        // Create companies
        $company1 = Company::factory()->create(['company_name' => 'Company A']);
        $company2 = Company::factory()->create(['company_name' => 'Company B']);

        // Super admin user
        $superAdmin = User::factory()->create([
            'user_role' => 'super admin',
            'company_id' => $company1->id,
        ]);

        // Another user to edit
        $userToEdit = User::factory()->create([
            'company_id' => $company2->id,
        ]);

        // Act
        $response = $this->actingAs($superAdmin)->get(route('users.edit', $userToEdit->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', $userToEdit);
        $response->assertViewHas('companies', function($companies) use ($company1, $company2) {
            return $companies->contains($company1) && $companies->contains($company2);
        });
    }

    /** @test */
    public function normal_user_can_access_edit_page_and_only_see_their_own_company()
    {
        // Create companies
        $company1 = Company::factory()->create(['company_name' => 'Company A']);
        $company2 = Company::factory()->create(['company_name' => 'Company B']);

        // Normal user
        $normalUser = User::factory()->create([
            'user_role' => 'staff',
            'company_id' => $company1->id,
        ]);

        // Another user to edit
        $userToEdit = User::factory()->create([
            'company_id' => $company2->id,
        ]);

        // Act
        $response = $this->actingAs($normalUser)->get(route('users.edit', $userToEdit->id));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertViewHas('user', $userToEdit);
        $response->assertViewHas('companies', function($companies) use ($company1, $company2) {
            // Must only contain company1, not company2
            return $companies->contains($company1) && !$companies->contains($company2);
        });
    }




        /** @test */
    public function it_updates_a_user_successfully()
    {
        $this->withoutExceptionHandling();

        // Create company
        $company = Company::factory()->create();

        // Create user to update
        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        // Acting as a super admin
        $admin = User::factory()->create([
            'user_role' => 'super admin',
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'newemail@example.com',
            'user_role' => 'admin',
            'company' => $company->id,
            'password' => 'newpassword123',
        ];

        $response = $this->put(route('users.update', $user->id), $data);

        $response->assertRedirect(route('users.index'))
                 ->assertSessionHas('success', 'User updated successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'John Doe',
            'email' => 'newemail@example.com',
            'company_id' => $company->id,
            'user_role' => 'admin',
        ]);

        // Check password was updated and hashed
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function it_does_not_update_password_if_not_provided()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company->id,
            'password' => Hash::make('oldpassword'),
        ]);

        $admin = User::factory()->create([
            'user_role' => 'super admin',
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin);

        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'user_role' => 'admin',
            'company' => $company->id,
            // password intentionally missing
        ];

        $this->put(route('users.update', $user->id), $data);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        // Old password still valid
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    /** @test */
    public function it_fails_validation_if_required_fields_are_missing()
    {
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $admin = User::factory()->create([
            'user_role' => 'super admin',
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin);

        $response = $this->from(route('users.edit', $user->id))
            ->put(route('users.update', $user->id), []); // empty data

        $response->assertRedirect(route('users.edit', $user->id));
        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'user_role', 'company']);
    }


    public function it_deletes_a_user_and_redirects_with_success_message()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->delete(route('users.destroy', $user->id));

        // Assert DB
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        // Assert redirect
        $response->assertRedirect(route('users.index'));

        // Assert flash message
        $response->assertSessionHas('success', 'User deleted successfully.');
    }


    /** @test */
    public function it_returns_users_for_ajax_call_search()
    {
        // Create a user and act as that user
        $authUser = User::factory()->create();
        $this->actingAs($authUser);

        // Create users in the same company
        $user1 = User::factory()->create([
            'company_id' => $authUser->company_id,
            'name' => 'Alice Johnson'
        ]);

        $user2 = User::factory()->create([
            'company_id' => $authUser->company_id,
            'name' => 'Bob Smith'
        ]);

        // Create a user in a different company (should not appear)
        $otherUser = User::factory()->create([
            'company_id' => $authUser->company_id + 1,
            'name' => 'Charlie Other'
        ]);

        // Case 1: Without search term
        $response = $this->getJson(route('ajax.users.call'));

        $response->assertStatus(200)
                ->assertJson([
                    'total' => 3, // Includes the authenticated user by default if applicable
                ]);

        $data = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('Alice Johnson', $data);
        $this->assertContains('Bob Smith', $data);
        $this->assertNotContains('Charlie Other', $data);

        // Case 2: With search term
        $response2 = $this->getJson(route('ajax.users.call', ['term' => 'Alice']));

        $response2->assertStatus(200);

        $filteredData = collect($response2->json('data'))->pluck('name')->toArray();
        $this->assertEquals(['Alice Johnson'], $filteredData);
    }




}
