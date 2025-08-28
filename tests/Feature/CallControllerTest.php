<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Call;
use App\Models\Company;



class CallControllerTest extends TestCase
{
   
    use RefreshDatabase;

    /** @test */
    public function it_returns_call_screen_with_empty_contact_list_when_contact_is_provided()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('call-screen', ['leadid' => 1, 'contact' => 'somecontact']));

        $response->assertStatus(200);
        $response->assertViewIs('call_screen');
        $response->assertViewHas('leadcontacts', []);
        $response->assertViewHas('contact', 'somecontact');
        $response->assertViewHas('leadid', 1);
    }

    /** @test */
    public function it_returns_call_screen_with_lead_contact_phones_when_contact_is_empty()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $lead = Lead::factory()->create();
        $contacts = Contact::factory()->count(3)->create();
        $lead->contacts()->attach($contacts->pluck('id')); // attach contacts to lead

        $response = $this->get(route('call-screen', ['leadid' => $lead->id, 'contact' => '']));

        $response->assertStatus(200);
        $response->assertViewIs('call_screen');
        $response->assertViewHas('leadcontacts', $contacts->pluck('phone'));
        $response->assertViewHas('contact', '');
        $response->assertViewHas('leadid', $lead->id);
    }


    /** @test */
    public function it_generates_a_twilio_token()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson(route('generate-twilio-token'));

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);

        $token = $response->json('token');

        // Optional: check if token is a non-empty string
        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }


    /** @test */
    public function it_requires_mandatory_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('store.call'));

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'name', 'date_start', 'time_start', 'date_end', 'time_end', 'duration'
                 ]);
    }

    
    /** @test */
    public function it_creates_a_call_successfully_without_lead()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Test Call',
            'date_start' => '28.08.2025',
            'time_start' => '10:00',
            'date_end' => '28.08.2025',
            'time_end' => '11:00',
            'duration' => 60,
            'status' => 'Planned',
            'direction' => 'Outbound',
            'parent_type' => 'Lead',
            'parent_name' => 'Test Lead',
            'description' => 'Call description',
            'assigned_user_name' => 'John Doe',
            'teams' => json_encode([1,2]),      // encode arrays as JSON
            'users' => json_encode([1]),
            'contacts' => json_encode([1]),
            'leads' => json_encode([1]),
        ];

        $this->withoutExceptionHandling(); // to see exact errors

        $response = $this->postJson(route('store.call'), $payload);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Call saved successfully!'
                ]);

        $this->assertDatabaseHas('calls', [
            'name' => 'Test Call',
            'status' => 'Planned',
            'direction' => 'Outbound',
            'duration' => 60,
            'parent_type' => 'Lead',
            'parent_name' => 'Test Lead',
            'description' => 'Call description',
            'assigned_user_name' => 'John Doe',
            'lead_id' => null,
            'company_id' => $user->company_id
        ]);
    }


    /** @test */
    public function it_creates_a_call_successfully_with_lead()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        $this->actingAs($user);

        $payload = [
            'name' => 'Call with Lead',
            'date_start' => '28.08.2025',
            'time_start' => '10:00',
            'date_end' => '28.08.2025',
            'time_end' => '11:00',
            'duration' => 60,
        ];

        $response = $this->postJson(route('store.call', ['lead' => $lead->id]), $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Call saved successfully!'
                 ]);

        $this->assertDatabaseHas('calls', [
            'name' => 'Call with Lead',
            'lead_id' => $lead->id,
            'company_id' => $user->company_id
        ]);
    }


    /** @test */
    public function it_shows_call_history_for_lead()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $company = Company::factory()->create();

        $lead = Lead::factory()->create([
            'company_id' => $company->id
        ]);

        $call = Call::factory()->create([
            'lead_id' => $lead->id,
            'status' => 'planned',
            'company_id' => $company->id
        ]);

        $response = $this->get(route('call.history', ['lead' => $lead->id]));

        $response->assertStatus(200)
            ->assertViewIs('leads.callhistory')
            ->assertViewHas('lead', $lead)
            ->assertViewHas('call', fn($viewCall) => $viewCall->id === $call->id);


        $specificCall = Call::factory()->create([
            'lead_id' => $lead->id,
            'status' => 'completed',
            'company_id' => $user->company_id
        ]);

        $response2 = $this->get(route('call.history', ['lead' => $lead->id, 'call' => $specificCall->id]));

        $response2->assertStatus(200)
            ->assertViewIs('leads.callhistory')
            ->assertViewHas('lead', $lead)
            ->assertViewHas('call', fn($viewCall) => $viewCall->id === $specificCall->id);
    }


  
    /** @test */
    public function it_updates_call_details_successfully()
    {
        // Create user and act as that user
        $user = User::factory()->create();
        $this->actingAs($user);
        $company=Company::factory()->create();


        // Create lead and a call belonging to the lead
        $lead = Lead::factory()->create(['company_id' => $company->id]);
        $call = Call::factory()->create([
            'lead_id' => $lead->id,
            'status' => 'planned'
        ]);

        $payload = [
            'transcript' => 'This is the call transcript.',
            'sentiment' => 'positive',
            'outcome' => 'Successful call',
            'audio_link' => 'https://example.com/audio.mp3',
            'call_id' => $call->id
        ];

        $response = $this->postJson(route('call.history.update', $lead->id), $payload);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Call details saved successfully!',
                    'redirect' => route('leads.show', $lead->id)
                ]);

        // Assert database updated
        $this->assertDatabaseHas('calls', [
            'id' => $call->id,
            'transcript' => $payload['transcript'],
            'sentiment' => $payload['sentiment'],
            'outcome' => $payload['outcome'],
            'audio_link' => $payload['audio_link'],
            'status' => 'held'
        ]);
    }

    /** @test */
    public function it_fails_to_update_if_call_does_not_belong_to_lead()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $company=Company::factory()->create();

        $lead1 = Lead::factory()->create(['company_id' => $company->id]);
        $lead2 = Lead::factory()->create(['company_id' => $company->id]);

        $call = Call::factory()->create([
            'lead_id' => $lead2->id,
            'status' => 'planned'
        ]);

        $payload = [
            'transcript' => 'Invalid attempt',
            'sentiment' => 'neutral',
            'outcome' => 'N/A',
            'audio_link' => 'https://example.com/audio.mp3',
            'call_id' => $call->id
        ];

        $response = $this->postJson(route('call.history.update', $lead1->id), $payload);

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => "Call not found or doesn't belong to this lead"
                ]);
    }


}
