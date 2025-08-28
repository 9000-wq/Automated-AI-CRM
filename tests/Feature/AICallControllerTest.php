<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\AiCall;
use App\Models\Lead;
use App\Models\Contact;


class AICallControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_ai_calls_view_for_non_ajax_requests()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin'
        ]);

        $response = $this->actingAs($user)->get(route('AiCalls'));

        $response->assertStatus(200);
        $response->assertViewIs('ai.calls');
    }

    /** @test */
    public function it_returns_ai_calls_data_for_ajax_request_as_super_admin()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin'
        ]);

        $lead = Lead::factory()->create();
        $contact = Contact::factory()->create();
        AiCall::factory()->create([
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('AiCalls'), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'recordsTotal',
            'recordsFiltered'
        ]);
    }

    /** @test */
    public function it_filters_ai_calls_by_company_for_non_super_admin()
    {
        $user = User::factory()->create([
            'user_role' => 'manager',
            'company_id' => 1
        ]);

        $lead = Lead::factory()->create();
        $contactInCompany = Contact::factory()->create(['company_id' => 1]);
        $contactOtherCompany = Contact::factory()->create(['company_id' => 2]);

        AiCall::factory()->create(['lead_id' => $lead->id, 'contact_id' => $contactInCompany->id]);
        AiCall::factory()->create(['lead_id' => $lead->id, 'contact_id' => $contactOtherCompany->id]);

        $response = $this->actingAs($user)->getJson(route('AiCalls'), [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);

        // Only contacts from user's company should appear
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($contactInCompany->id, $data[0]['contact_id']);
    }
}
