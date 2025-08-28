<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Plan;
use App\Models\CompanyPlan;


class HomeControllerTest extends TestCase
{
   
    use RefreshDatabase;

    /** @test */
    public function authenticated_and_verified_user_can_access_dashboard()
    {
        // create a verified user
        $user = User::factory()->create([
            'email_verified_at' => now(), // mark as verified
        ]);

        // acting as the user
        $response = $this->actingAs($user)->get(route('dashboard'));

        // assert dashboard page loads
        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_login()
    {
        // no user login
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

   
    /** @test */
    public function it_displays_authenticated_users_company_info()
    {
        // Create a company
        $company = Company::factory()->create(['company_name' => 'Test Company']);

        // Create a user belonging to this company
        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act as the user
        $this->actingAs($user);

        // Make GET request to the route
        $response = $this->get(route('companyinfo'));

        // Assert the response is OK and the correct view is returned
        $response->assertStatus(200);
        $response->assertViewIs('companyinfo');
        $response->assertViewHas('companyinfo', $company);
    }


    /** @test */
    public function it_updates_company_info_successfully()
    {
        // Create a company
        $company = Company::factory()->create([
            'company_name' => 'Old Name',
            'company_email' => 'old@example.com',
            'company_address' => 'Old Address',
            'country' => 'Old Country',
            'company_description' => 'Old Description',
            'business_type' => 'service',
            'price_guidelines' => 'Old Price',
            'bussiness_knowledge' => 'Old Knowledge',
        ]);

        // Create a user associated with this company
        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        // Prepare payload for service type company
        $payload = [
            'companyid' => $company->id,
            'companyName' => 'New Company Name',
            'company_email' => 'new@example.com',
            'companyAddress' => 'New Address',
            'country' => 'New Country',
            'companyDescription' => 'New Description',
            'business_type' => 'service',
            'serviceKnowledge' => 'Updated Service Knowledge',
            'priceGuidelines' => 'Updated Price',
        ];

        // Send POST request
        $response = $this->post(route('updatecompanyinfo', ['companyid' => $company->id]), $payload);

        // Assert redirect to companyinfo
        $response->assertRedirect(route('companyinfo'));
        $response->assertSessionHas('success', 'User updated successfully.');

        // Assert database updated correctly
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'company_name' => 'New Company Name',
            'company_email' => 'new@example.com',
            'company_address' => 'New Address',
            'country' => 'New Country',
            'company_description' => 'New Description',
            'business_type' => 'service',
            'price_guidelines' => 'Updated Price',
            'bussiness_knowledge' => 'Updated Service Knowledge',
        ]);
    }

    /** @test */
    public function it_updates_company_info_for_product_type_company()
    {
        $company = Company::factory()->create(['business_type' => 'product']);
        $user = User::factory()->create(['company_id' => $company->id]);
        $this->actingAs($user);

        $payload = [
            'companyid' => $company->id,
            'companyName' => 'Product Company',
            'company_email' => 'product@example.com',
            'companyAddress' => 'Product Address',
            'country' => 'Product Country',
            'companyDescription' => 'Product Description',
            'business_type' => 'product',
            'productKnowledge' => 'Product Knowledge Updated',
            'priceGuidelines' => 'Product Price Updated',
        ];

        $response = $this->post(route('updatecompanyinfo', ['companyid' => $company->id]), $payload);

        $response->assertRedirect(route('companyinfo'));
        $response->assertSessionHas('success', 'User updated successfully.');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'company_name' => 'Product Company',
            'bussiness_knowledge' => 'Product Knowledge Updated',
            'price_guidelines' => 'Product Price Updated',
        ]);
    }


    /** @test */
    public function it_returns_company_data_for_manageprices_ajax_request()
    {
        // Create a user with super admin role
        $user = User::factory()->create([
            'user_role' => 'super admin'
        ]);

        $this->actingAs($user);

        // Create some companies
        $companies = Company::factory()->count(3)->create();

        // AJAX request
        $response = $this->getJson(route('manageprices'), ['HTTP_X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

        $jsonData = $response->json();

        // Assert that data contains companies
        $this->assertArrayHasKey('data', $jsonData);
        $this->assertCount(3, $jsonData['data']);

        // Assert that 'action' column exists for each row
        foreach ($jsonData['data'] as $row) {
            $this->assertArrayHasKey('action', $row);
            $this->assertStringContainsString('New Plan', $row['action']);
            $this->assertStringContainsString('See All Plans', $row['action']);
        }
    }

    /** @test */
    public function it_returns_prices_view_for_non_ajax_request()
    {

         // Create a user with super admin role
        $user = User::factory()->create([
            'user_role' => 'super admin'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('manageprices'));

        $response->assertStatus(200);
        $response->assertViewIs('prices');
    }



    /** @test */
    public function it_loads_newplan_view_with_companyid_and_plans()
    {
        // Create a user with the required role
        $user = User::factory()->create([
            'user_role' => 'super admin'
        ]);
        $this->actingAs($user);

        // Create some plans
        $plans = Plan::factory()->count(3)->create();

        // Provide a company ID for the route
        $companyId = 1;

        // Make the GET request
        $response = $this->get(route('newplan', ['companyid' => $companyId]));

        // Assert the response is OK
        $response->assertStatus(200);

        // Assert the view is correct
        $response->assertViewIs('newplan');

        // Assert view has the companyid
        $response->assertViewHas('companyid', $companyId);

        // Assert view has plans
        $response->assertViewHas('plans', function($viewPlans) use ($plans) {
            return $viewPlans->count() === $plans->count();
        });
    }



    /** @test */
    public function it_saves_a_new_plan_for_company()
    {
        // Create a user and acting as that user
        $user = User::factory()->create([
            'user_role' => 'super admin', // based on your middleware
        ]);
        $this->actingAs($user);

        // Create a company
        $company = Company::factory()->create();

        // Create a plan
        $plan = Plan::factory()->create();

        $payload = [
            'companyid' => $company->id,
            'plnaname' => $plan->id,
            'price' => 100,
            'startdate' => '2025-08-27',
            'enddate' => '2025-09-27',
        ];

        $response = $this->postJson(route('savenewplan'), $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Plan Activate Successfully,',
                 ]);

        // Assert the CompanyPlan entry is created
        $this->assertDatabaseHas('company_plans', [
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'start_date' => '2025-08-27',
            'end_date' => '2025-09-27',
            'custom_price' => 100,
        ]);
    }


    /** @test */
    public function it_deletes_a_company_plan_successfully()
    {
        // Create a super admin user
        $user = User::factory()->create([
            'user_role' => 'super admin', // based on your middleware
        ]);
        $this->actingAs($user);

        // Create a company and plan
        $company = Company::factory()->create();
        $plan = Plan::factory()->create();

        // Create a company plan
        $companyPlan = CompanyPlan::create([
            'company_id' => $company->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'custom_price' => 100,
        ]);

        $payload = [
            'planid' => $companyPlan->id
        ];

        $response = $this->postJson(route('deletenewplan'), $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Plan Inactive Successfully.'
                 ]);

        // Assert the CompanyPlan entry is deleted
        $this->assertDatabaseMissing('company_plans', [
            'id' => $companyPlan->id,
        ]);
    }



   /** @test */
    public function it_displays_all_plans_for_a_company()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        $company = Company::factory()->create();

        $plan1 = Plan::factory()->create(['name' => 'Basic Plan', 'price' => 100]);
        $plan2 = Plan::factory()->create(['name' => 'Premium Plan', 'price' => 200]);

        CompanyPlan::create([
            'company_id' => $company->id,
            'plan_id' => $plan1->id,
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'custom_price' => 90,
        ]);

        CompanyPlan::create([
            'company_id' => $company->id,
            'plan_id' => $plan2->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->addMonth(),
            'custom_price' => 180,
        ]);

        $response = $this->get(route('seeallplans', ['companyid' => $company->id]));

        $response->assertStatus(200)
                ->assertViewIs('seeallplans')
                ->assertViewHas('plans');

        // Check that plan names exist in the view using the correct property
        $plansInView = $response->viewData('plans');
        $planNames = $plansInView->pluck('plan_name')->toArray();

        $this->assertContains('Basic Plan', $planNames);
        $this->assertContains('Premium Plan', $planNames);
    }


}
