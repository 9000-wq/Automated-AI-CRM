<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use App\Models\EmailLog;

class EmailControllerTest extends TestCase
{
   
    use RefreshDatabase;

    /** @test */
    public function it_displays_email_logs_page()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as the user
        $this->actingAs($user);

        // Send GET request to email logs route
        $response = $this->get(route('email-logs'));

        // Assert status and view
        $response->assertStatus(200);
        $response->assertViewIs('leads.emailindex');
    }



    /** @test */
    public function it_returns_email_logs_data_for_datatables()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        
        // Create some email logs
        $logs = EmailLog::factory()->count(3)->create([
            'lead_id' => $lead->id,
        ]);

        $this->actingAs($user);

        // Call route without filter
        $response = $this->getJson(route('email-logs-data'));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'lead_id', 'body', 'created_at', 'updated_at', 'parent', 'action']
                    ]
                ]);

        // Call route with lead_id filter
        $responseFiltered = $this->getJson(route('email-logs-data', ['lead_id' => $lead->id]));

        $responseFiltered->assertStatus(200)
                        ->assertJsonStructure([
                            'data' => [
                                '*' => ['id', 'lead_id', 'body', 'created_at', 'updated_at', 'parent', 'action']
                            ]
                        ]);

        // Check that the filtered response only includes logs for this lead
        $data = $responseFiltered->json('data');
        foreach ($data as $log) {
            $this->assertEquals($lead->id, $log['lead_id']);
        }
    }


    /** @test */
    public function it_returns_leads_based_on_company_and_search_term()
    {
        // Create a user
        $user = User::factory()->create(['company_id' => 1]);
        
        // Create leads belonging to the same company
        $lead1 = Lead::factory()->create(['company_id' => 1, 'name' => 'Alpha Lead']);
        $lead2 = Lead::factory()->create(['company_id' => 1, 'name' => 'Beta Lead']);
        
        // Create a lead for a different company
        $otherLead = Lead::factory()->create(['company_id' => 2, 'name' => 'Other Company Lead']);

        $this->actingAs($user);

        // Call without search term
        $response = $this->getJson(route('ajax.leads'));
        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $lead1->id, 'name' => $lead1->name])
                ->assertJsonFragment(['id' => $lead2->id, 'name' => $lead2->name])
                ->assertJsonMissing(['id' => $otherLead->id]);

        // Call with search term
        $response = $this->getJson(route('ajax.leads', ['term' => 'Alpha']));
        $response->assertStatus(200)
                ->assertJsonFragment(['id' => $lead1->id, 'name' => $lead1->name])
                ->assertJsonMissing(['id' => $lead2->id])
                ->assertJsonMissing(['id' => $otherLead->id]);
    }




}
