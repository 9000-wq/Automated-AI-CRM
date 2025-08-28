<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\AiEmail;
use App\Models\Lead;
use App\Models\Contact;


class AIEmailControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_returns_aiemails_view_when_not_ajax()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('AiEmails'))
            ->assertStatus(200)
            ->assertViewIs('ai.emails');
    }

    /** @test */
    public function super_admin_can_get_all_aiemails_via_ajax()
    {
        $user = User::factory()->create([
            'user_role' => 'super admin',
        ]);

        $lead = Lead::factory()->create();
        $contact = Contact::factory()->create();
        $aiEmail = AiEmail::factory()->create([
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('AiEmails'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'leadname' => $lead->name,
            'contactname' => $contact->name,
        ]);
    }

    /** @test */
    public function normal_user_only_sees_aiemails_for_their_company_via_ajax()
    {
        $companyId = 1;

        $user = User::factory()->create([
            'user_role' => 'user',
            'company_id' => $companyId,
        ]);

        $lead = Lead::factory()->create();
        $contactSameCompany = Contact::factory()->create(['company_id' => $companyId]);
        $contactOtherCompany = Contact::factory()->create(['company_id' => 999]);

        $aiEmailSameCompany = AiEmail::factory()->create([
            'lead_id' => $lead->id,
            'contact_id' => $contactSameCompany->id,
        ]);

        $aiEmailOtherCompany = AiEmail::factory()->create([
            'lead_id' => $lead->id,
            'contact_id' => $contactOtherCompany->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson(route('AiEmails'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);

        // ✅ Should see only same company contact's AiEmail
        $response->assertJsonFragment(['contactname' => $contactSameCompany->name]);
        $response->assertJsonMissing(['contactname' => $contactOtherCompany->name]);
    }

}
