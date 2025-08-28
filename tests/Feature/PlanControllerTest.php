<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Plan;
use App\Models\User;

class PlanControllerTest extends TestCase
{
   
    use RefreshDatabase;

    
    /** @test */
    public function it_displays_all_plans_on_index_page()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        // Create all plans with exact billing_cycle values
        $plans = collect([
            ['name' => 'Basic Plan', 'description' => 'Basic plan description', 'price' => 100, 'billing_cycle' => 'monthly', 'features' => 'Feature 1'],
            ['name' => 'Standard Plan', 'description' => 'Standard plan description', 'price' => 200, 'billing_cycle' => 'monthly', 'features' => 'Feature 1, Feature 2'],
            ['name' => 'Premium Plan', 'description' => 'Premium plan description', 'price' => 300, 'billing_cycle' => 'yearly', 'features' => 'All features'],
        ])->map(function ($data) {
            return Plan::create($data);
        });

        $response = $this->get(route('plans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('plans.index');
        $response->assertViewHas('plans', function ($viewPlans) use ($plans) {
            return $viewPlans->count() === $plans->count();
        });

        // Assert all plan names appear in the view
        $response->assertViewHas('plans', function ($viewPlans) {
            foreach ($viewPlans as $plan) {
                $this->assertNotEmpty($plan->name); // or assert something else
            }
            return true;
        });

    }



    /** @test */
    public function it_displays_the_create_plan_view()
    {
        // Acting as a user (if route requires auth, otherwise remove)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('plans.create'));

        $response->assertStatus(200);
        $response->assertViewIs('plans.create');
    }


    /** @test */
    public function it_creates_a_new_plan_successfully()
    {
        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);


        $payload = [
            'name' => 'Test Plan',
            'description' => 'This is a test plan',
            'price' => 99.99,
            'billing_cycle' => 'monthly', // Must match 'monthly' or 'yearly'
            'features' => "Feature 1\nFeature 2\nFeature 3"
        ];

        $response = $this->post(route('plans.store'), $payload);

        // Assert that the plan is saved in the database
        $this->assertDatabaseHas('plans', [
            'name' => 'Test Plan',
            'description' => 'This is a test plan',
            'price' => 99.99,
            'billing_cycle' => 'monthly',
            'features' => 'Feature 1,Feature 2,Feature 3'
        ]);

        // Assert redirect to plans index with success message
        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('success', 'Plan created successfully.');
    }



    /** @test */
    public function it_shows_the_edit_form_for_a_plan()
    {
        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        // Arrange: create a plan in the DB
        $plan = Plan::factory()->create();

        // Act: hit the route with the plan
        $response = $this->get(route('plans.edit', $plan));

        // Assert: response is OK and view has plan
        $response->assertStatus(200);
        $response->assertViewIs('plans.edit');
        $response->assertViewHas('plan', function ($viewPlan) use ($plan) {
            return $viewPlan->id === $plan->id;
        });
    }

    /** @test */
    public function it_handles_edit_route_without_plan_id()
    {

         // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);


        // Act: call the route with no id
        $response = $this->get(route('plans.edit'));

        // Assert: page loads (depending on your logic)
        // If optional plan is allowed, expect 200
        $response->assertStatus(200);
        $response->assertViewIs('plans.edit');
        $response->assertViewHas('plan'); // may be null
    }

    /** @test */
    public function it_updates_a_plan_successfully()
    {

        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        // Arrange: create a plan
        $plan = Plan::factory()->create([
            'name' => 'Old Name',
            'description' => 'Old Description',
            'price' => 10,
            'billing_cycle' => 'monthly',
            'features' => 'Old Feature',
        ]);

        // Act: send update request
        $response = $this->put(route('plans.update', $plan), [
            'name' => 'New Plan Name',
            'description' => 'Updated Description',
            'price' => 99.99,
            'billing_cycle' => 'yearly',
            'features' => "Feature A\nFeature B\nFeature C", // multiline string
        ]);

        // Assert: check redirect + success message
        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('success', 'Plan updated successfully.');

        // Assert: DB actually updated
        $this->assertDatabaseHas('plans', [
            'id' => $plan->id,
            'name' => 'New Plan Name',
            'description' => 'Updated Description',
            'price' => 99.99,
            'billing_cycle' => 'yearly',
            // features should be comma-separated after processing
            'features' => 'Feature A,Feature B,Feature C',
        ]);
    }

    /** @test */
    public function it_fails_validation_when_required_fields_are_missing()
    {

        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        $plan = Plan::factory()->create();

        $response = $this->from(route('plans.edit', $plan))
                         ->put(route('plans.update', $plan), [
                             // no data passed
                         ]);

        $response->assertRedirect(route('plans.edit', $plan));
        $response->assertSessionHasErrors(['name', 'price', 'billing_cycle', 'features']);
    }



    /** @test */
    public function it_deletes_a_plan_successfully()
    {

        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);

        // Arrange: create a plan in the DB
        $plan = Plan::factory()->create();

        // Act: send DELETE request with planid
        $response = $this->delete(route('plans.destroy'), [
            'planid' => $plan->id,
        ]);

        // Assert: redirect back to index with success
        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('success', 'Plan deleted successfully.');

        // Assert: Plan no longer exists in DB
        $this->assertDatabaseMissing('plans', [
            'id' => $plan->id,
        ]);
    }

    /** @test */
    public function it_returns_no_error_if_plan_does_not_exist()
    {

        // Acting as a user (if route requires auth)
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);
        $this->actingAs($user);
        
        // Act: try deleting a non-existing plan
        $response = $this->delete(route('plans.destroy'), [
            'planid' => 9999,
        ]);

        // Assert: redirect back with success
        $response->assertRedirect(route('plans.index'));
        $response->assertSessionHas('success', 'Plan deleted successfully.');

        // DB should still be empty
        $this->assertDatabaseCount('plans', 0);
    }



}
