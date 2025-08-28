<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\Account;
use App\Models\Call;
use App\Models\Company;
use App\Models\ContactRole;
use App\Models\LeadContact;
use App\Models\Note;
use App\Mail\LeadEmail;
use App\Models\EmailLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;



class LeadControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_opportunities_page()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as that user
        $this->actingAs($user);

        // Hit the route
        $response = $this->get(route('Opportunities'));

        // Assert response is 200 OK
        $response->assertStatus(200);

        // Assert the correct view is loaded
        $response->assertViewIs('opportunities');

        // Optionally check for some unique text in the view
        $response->assertSee('Opportunities');
    }


    /** @test */
    public function it_fetches_leads_filtered_by_status_and_company()
    {
        // Create a user and authenticate
        $user = User::factory()->create([
            'company_id' => 1,
        ]);

        $this->actingAs($user);

        // Leads with different statuses and companies
        $lead1 = Lead::factory()->create([
            'status' => 'open',
            'company_id' => 1,
            'name' => 'Lead A',
        ]);

        $lead2 = Lead::factory()->create([
            'status' => 'closed',
            'company_id' => 1,
            'name' => 'Lead B',
        ]);

        $lead3 = Lead::factory()->create([
            'status' => 'open',
            'company_id' => 2, // different company, should not appear
            'name' => 'Lead C',
        ]);

        // Call the route with status = open
        $response = $this->get(route('leads.fetch', [
            'status' => 'open',
            'page'   => 1,
        ]));

        // Assert response status
        $response->assertStatus(200);

        // If your controller returns a Blade view (like a partial)
        if ($response->original instanceof \Illuminate\View\View) {
            $response->assertViewIs('layouts.leads');
            $response->assertSee('Lead A');
            $response->assertDontSee('Lead B');
            $response->assertDontSee('Lead C');
        }

        // If your controller returns JSON instead
        if ($response->headers->get('content-type') === 'application/json') {
            $response->assertJsonFragment(['name' => 'Lead A']);
            $response->assertJsonMissing(['name' => 'Lead B']);
            $response->assertJsonMissing(['name' => 'Lead C']);
        }
    }

    /** @test */
    public function it_allows_searching_leads_by_name_or_case_ref_or_source()
    {
        $user = User::factory()->create(['company_id' => 1]);
        $this->actingAs($user);

        $lead1 = Lead::factory()->create([
            'status' => 'open',
            'company_id' => 1,
            'name' => 'Special Lead',
        ]);

        $lead2 = Lead::factory()->create([
            'status' => 'open',
            'company_id' => 1,
            'name' => 'Other Lead',
        ]);

        // Search by keyword "Special"
        $response = $this->get(route('leads.fetch', [
            'status' => 'open',
            'search' => 'Special',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Special Lead');
        $response->assertDontSee('Other Lead');
    }


    /** @test */
    public function super_admin_can_view_all_leads_via_ajax()
    {
        // Create a super admin user
        $superAdmin = User::factory()->create([
            'user_role' => 'super admin',
        ]);

        // Create leads belonging to different companies
        $lead1 = Lead::factory()->create(['company_id' => 1]);
        $lead2 = Lead::factory()->create(['company_id' => 2]);

        // Act as super admin and make ajax request
        $response = $this->actingAs($superAdmin)
            ->getJson(route('leads.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $lead1->id]);
        $response->assertJsonFragment(['id' => $lead2->id]);
    }

    /** @test */
    public function admin_can_only_view_leads_of_their_company()
    {
        // Create an admin user with company_id = 1
        $admin = User::factory()->create([
            'user_role' => 'admin',
            'company_id' => 1,
        ]);

        // Leads from company 1 and another company
        $lead1 = Lead::factory()->create(['company_id' => 1]);
        $lead2 = Lead::factory()->create(['company_id' => 2]);

        // Act as admin and make ajax request
        $response = $this->actingAs($admin)
            ->getJson(route('leads.index'), ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $lead1->id]);
        $response->assertJsonMissing(['id' => $lead2->id]); // should not see other company's leads
    }

    /** @test */
    public function guest_cannot_access_leads_index()
    {
        $response = $this->get(route('leads.index'));

        $response->assertRedirect(route('login'));
    }

     /** @test */
    public function it_shows_only_users_from_the_authenticated_users_company_in_create_view()
    {
        // Company 1 users
        $companyUser = User::factory()->create([
            'company_id' => 1,
        ]);
        $userInSameCompany = User::factory()->create([
            'company_id' => 1,
        ]);

        // Company 2 user
        $userInOtherCompany = User::factory()->create([
            'company_id' => 2,
        ]);

        // Act as a user from company 1
        $response = $this->actingAs($companyUser)
            ->get(route('leads.create'));

        $response->assertStatus(200);
        $response->assertViewIs('leads.create');

        // Ensure view has the "users" variable
        $response->assertViewHas('users', function ($users) use ($userInSameCompany, $userInOtherCompany) {
            // Must contain same company user
            $this->assertTrue($users->contains('id', $userInSameCompany->id));
            // Must NOT contain other company user
            $this->assertFalse($users->contains('id', $userInOtherCompany->id));
            return true;
        });
    }

    /** @test */
    public function it_creates_a_lead_with_basic_fields()
    {
        $user = User::factory()->create([
            'company_id' => 1
        ]);

        $payload = [
            'case_ref' => 'CASE123',
            'name' => 'Test Lead',
            'source' => 'Website',
            'status' => 'New',
            'assigned_to' => null,
        ];

        $this->actingAs($user)
            ->post(route('leads.store'), $payload)
            ->assertRedirect(route('leads.index'))
            ->assertSessionHas('success', 'Lead created successfully.');

        $this->assertDatabaseHas('leads', [
            'case_ref' => 'CASE123',
            'company_id' => 1,
        ]);
    }

    /** @test */
    public function it_creates_lead_and_links_existing_contacts_if_account_given()
    {

        $account = Account::factory()->create();
        $user = User::factory()->create(['company_id' => 1]);    
        $contact = Contact::factory()->create([
            'account_id' => $account->id,
            'company_id' => $user->company_id,
            'lead_id' => null, // safe
            // no need to pass contact_role_id, factory will create one
        ]);

        
        $payload = [
            'case_ref' => 'CASE124',
            'name' => 'Lead with account',
            'source' => 'Referral',
            'status' => 'Open',
            'account' => $account->id,
        ];

        $this->actingAs($user)->post(route('leads.store'), $payload);

        $lead = Lead::where('case_ref', 'CASE124')->first();
        $this->assertDatabaseHas('lead_contacts', [
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
            'account_id' => $account->id,
        ]);
    }

    /** @test */
    public function it_creates_lead_and_new_contacts_from_request()
    {
        $user = User::factory()->create(['company_id' => 1]);

        $payload = [
            'case_ref' => 'CASE125',
            'name' => 'Lead with contacts',
            'source' => 'Email',
            'status' => 'Pending',
            'contacts' => [
                [
                    'full_name' => 'John Doe',
                    'email' => 'john@example.com',
                    'birthday' => '1990-01-01',
                    'phone' => '123456789',
                    'description' => 'Test contact',
                    'address' => '123 Main St',
                    'role' => 'Manager'
                ]
            ]
        ];

        $this->actingAs($user)->post(route('leads.store'), $payload);

        $lead = Lead::where('case_ref', 'CASE125')->first();
        $contact = Contact::where('email', 'john@example.com')->first();

        $this->assertDatabaseHas('contacts', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company_id' => 1,
        ]);

        $this->assertDatabaseHas('lead_contacts', [
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        $this->assertDatabaseHas('contact_roles', [
            'label' => 'Manager',
        ]);
    }



    /** @test */
    public function it_displays_lead_with_activities_and_history()
    {

        $user = User::factory()->create(['company_id' => 1]);
        $this->actingAs($user);


        // Create a lead
        $lead = Lead::factory()->create();

        // Create planned call (activity)
        $plannedCall = Call::factory()->create([
            'lead_id' => $lead->id,
            'status' => 'planned',
            'date_start' => now()->addDay(),
        ]);

        // Create held call (history)
        $heldCall = Call::factory()->create([
            'lead_id' => $lead->id,
            'status' => 'held',
            'date_start' => now()->subDay(),
        ]);

        // Create another lead + call to ensure filtering works
        $otherLead = Lead::factory()->create();
        $otherCall = Call::factory()->create([
            'lead_id' => $otherLead->id,
            'status' => 'planned',
        ]);

        // Hit the route
        $response = $this->get(route('leads.show', $lead));

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('leads.show');
        $response->assertViewHas('lead', $lead);

        $response->assertViewHas('activities', function ($activities) use ($plannedCall, $otherCall) {
            return $activities->contains($plannedCall)   // should include planned call of this lead
                && !$activities->contains($otherCall);   // should NOT include other lead’s call
        });

        $response->assertViewHas('history', function ($history) use ($heldCall) {
            return $history->contains($heldCall); // should include held call
        });
    }



    /** @test */
    public function it_shows_the_edit_page_for_a_lead()
    {
        // Create a company user
        $user = User::factory()->create();
        $company = Company::factory()->create();
        
        // Create a lead belonging to the same company
        $lead = Lead::factory()->create([
            'company_id' => $company->id,
        ]);

        // Act as the user
        $response = $this->actingAs($user)
            ->get(route('leads.edit', $lead));

        // Assert correct view is returned
        $response->assertStatus(200);
        $response->assertViewIs('leads.edit');

        // Assert data passed to view
        $response->assertViewHas('lead', $lead);
        $response->assertViewHas('users', function ($users) use ($user) {
            // Check that returned users belong to same company
            return $users->contains('id', $user->id);
        });
    }

    /** @test */
    public function guest_users_cannot_access_lead_edit()
    {
        $lead = Lead::factory()->create();

        $response = $this->get(route('leads.edit', $lead));

        $response->assertRedirect(route('login'));
    }



    /** @test */
    public function it_updates_a_lead_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a lead
        $lead = Lead::factory()->create([
            'case_ref' => 'OLD123',
            'name' => 'Old Lead',
            'source' => 'Old Source',
            'status' => 'Open',
            'assigned_to' => null,
        ]);

        // Prepare updated data
        $updatedData = [
            'case_ref' => 'NEW123',
            'name' => 'Updated Lead',
            'source' => 'Updated Source',
            'status' => 'Closed',
            'assigned_to' => $user->id,
        ];

        // Hit the update route
        $response = $this->put(route('leads.update', $lead->id), $updatedData);

        // Assert redirect
        $response->assertRedirect(route('leads.index'));
        $response->assertSessionHas('success', 'Lead updated successfully.');

        // Assert database updated
        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'case_ref' => 'NEW123',
            'name' => 'Updated Lead',
            'source' => 'Updated Source',
            'status' => 'Closed',
            'assigned_to' => $user->id,
        ]);
    }




    /** @test */
    public function it_deletes_a_lead_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a lead that belongs to this user's company
        $lead = Lead::factory()->create([
            'company_id' => Company::factory(),
        ]);

        // Send DELETE request with lead ID in request body
        $response = $this->delete(route('leads.destroy'), [
            'leadid' => $lead->id,
        ]);

        // Assert the lead is deleted from database
        $this->assertDatabaseMissing('leads', [
            'id' => $lead->id,
        ]);

        // Assert redirect back with success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Lead deleted successfully.');
    }

    /** @test */
    public function it_fails_if_lead_does_not_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->delete(route('leads.destroy'), [
            'leadid' => 9999,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Lead not found.');

        $this->assertDatabaseCount('leads', 0);
    }



    /** @test */
    public function it_returns_empty_contact_when_no_params_are_passed()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('leadcontact'));

        $response->assertStatus(200);
        $response->assertViewIs('leads.editcontact');
        $response->assertViewHas('contact', []);
        $response->assertViewHas('leadid', null);
        $response->assertViewHas('lead_id', null);
    }

    /** @test */
    public function it_does_not_fetch_contact_when_contact_contains_lead()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('leadcontact', ['contact' => 'lead123', 'leadid' => 5]));

        $response->assertStatus(200);
        $response->assertViewIs('leads.editcontact');
        $response->assertViewHas('contact', []);
        $response->assertViewHas('leadid', 'lead123');
        $response->assertViewHas('lead_id', 5);
    }

    /** @test */
    public function it_fetches_contact_when_valid_contact_id_is_provided()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $contact = Contact::factory()->create();

        $response = $this->get(route('leadcontact', ['contact' => $contact->id, 'leadid' => 10]));

        $response->assertStatus(200);
        $response->assertViewIs('leads.editcontact');
        $response->assertViewHas('contact', function ($c) use ($contact) {
            return $c->first()->id === $contact->id;
        });
        $response->assertViewHas('leadid', $contact->id);
        $response->assertViewHas('lead_id', 10);
    }

    /** @test */
    public function it_returns_empty_contact_when_contact_id_does_not_exist()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('leadcontact', ['contact' => 9999, 'leadid' => 7]));

        $response->assertStatus(200);
        $response->assertViewIs('leads.editcontact');
        $response->assertViewHas('contact', function ($contact) {
            return $contact->isEmpty(); // works for Eloquent\Collection
        });
        $response->assertViewHas('leadid', 9999);
        $response->assertViewHas('lead_id', 7);
    }


    /** @test */
    public function it_updates_an_existing_contact()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $lead = Lead::factory()->create();
        $role = ContactRole::factory()->create();

        $contact = Contact::factory()->create([
            'company_id' => $user->company_id,
            'contact_role_id' => $role->id,
        ]);

        $payload = [
            'contactid'   => $contact->id,  // existing contact
            'full_name'   => 'Updated Contact',
            'phone'       => '1234567890',
            'email'       => 'updated@example.com',
            'role'        => 'Manager',
            'address'     => '123 Test St',
            'description' => 'Updated description',
            'birthday'    => '1990-01-01',
            'leadid'      => $lead->id,
        ];

        $response = $this->put(route('updateleadcontact'), $payload);

        $response->assertRedirect(route('leads.show', ['lead' => $lead->id]));

        $this->assertDatabaseHas('contacts', [
            'id'      => $contact->id,
            'name'    => 'Updated Contact',
            'phone'   => '1234567890',
            'email'   => 'updated@example.com',
            'address' => '123 Test St',
        ]);
    }

    /** @test */
    public function it_creates_a_new_contact_and_links_it_to_lead()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $lead = Lead::factory()->create();

        $payload = [
            'contactid'   => 'lead' . $lead->id, // triggers create branch
            'full_name'   => 'New Contact',
            'phone'       => '9876543210',
            'email'       => 'new@example.com',
            'role'        => 'Director',
            'address'     => '456 Example Ave',
            'description' => 'New description',
            'birthday'    => '1985-05-05',
            'leadid'      => $lead->id,
            'account_id'  => 1,
        ];

        $response = $this->put(route('updateleadcontact'), $payload);

        $response->assertRedirect(route('leads.index'));

        $this->assertDatabaseHas('contacts', [
            'name'    => 'New Contact',
            'email'   => 'new@example.com',
            'phone'   => '9876543210',
        ]);

        $this->assertDatabaseHas('lead_contacts', [
            'lead_id'    => $lead->id,
            'contact_id' => Contact::latest()->first()->id,
        ]);
    }




     /** @test */
    public function it_deletes_a_lead_contact_successfully()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a lead and a contact
        $lead = Lead::factory()->create();
        $contact = Contact::factory()->create();

        // Create pivot record
        $leadContact = LeadContact::create([
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        // Assert it exists before deletion
        $this->assertDatabaseHas('lead_contacts', [
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        // Call the route
        $response = $this->delete(route('deleteleadcontact', [
            'contact' => $contact->id,
            'lead' => $lead->id,
        ]));

        // Assert record is deleted
        $this->assertDatabaseMissing('lead_contacts', [
            'lead_id' => $lead->id,
            'contact_id' => $contact->id,
        ]);

        // Assert redirect and success message
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Contact deleted successfully');
    }

    

    /** @test */
    public function it_saves_notes_for_a_lead()
    {
        // Create a user and a lead
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        $this->actingAs($user);

        $payload = [
            'notes' => 'This is a test note.',
            'leadid' => $lead->id,
        ];

        // Send POST request
        $response = $this->postJson(route('leads.savenotes'), $payload);

        // Assert database has the note
        $this->assertDatabaseHas('notes', [
            'lead_id' => $lead->id,
            'user_id' => $user->id,
            'notes' => 'This is a test note.',
        ]);

        // Assert JSON response
        $response->assertStatus(200)
                 ->assertJson([
                     'Success' => 'Notes Added Successfully'
                 ]);
    }

    /** @test */
    public function it_fails_validation_if_notes_are_missing()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        $this->actingAs($user);

        $payload = [
            'notes' => '',
            'leadid' => $lead->id,
        ];

        $response = $this->postJson(route('leads.savenotes'), $payload);

        $response->assertStatus(422); // Laravel validation failure
        $response->assertJsonValidationErrors('notes');
    }


    /** @test */
    public function it_fetches_notes_for_a_lead()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        // Create multiple notes for this lead
        Note::factory()->count(5)->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('leads.notes', $lead->id));

        $response->assertStatus(200);
        $response->assertViewIs('leads.shownotes');
        $response->assertViewHas('notes');
        $response->assertViewHas('leadId', $lead->id);
        $response->assertViewHas('pageno', null); // default page is null if not provided
    }

    /** @test */
    public function it_accepts_page_parameter()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();

        Note::factory()->count(15)->create([
            'lead_id' => $lead->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('leads.notes', $lead->id) . '?page=2');

        $response->assertStatus(200);
        $response->assertViewIs('leads.shownotes');
        $response->assertViewHas('pageno', 2);
        $response->assertViewHas('notes');
    }



    /** @test */
    public function it_deletes_a_note_successfully()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create(); // create a valid lead

        $note = Note::factory()->create([
            'user_id' => $user->id,
            'lead_id' => $lead->id, // ensure FK is valid
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson(route('deletenotes'), ['noteid' => $note->id]);

        $response->assertStatus(200)
                ->assertJson(['success' => 'Note Deleted Successfully.']);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    /** @test */
    public function it_returns_error_if_note_does_not_exist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->deleteJson(route('deletenotes'), ['noteid' => 9999]);

        $response->assertStatus(404)
                ->assertJson(['error' => 'Note not found.']);
    }


    /** @test */
    public function it_fetches_accounts_based_on_search_query()
    {
        // Create a user
        $user = User::factory()->create();

        // Create some accounts
        $account1 = Account::factory()->create(['name' => 'Alpha Corp', 'email' => 'alpha@example.com']);
        $account2 = Account::factory()->create(['name' => 'Beta Ltd', 'email' => 'beta@example.com']);
        $account3 = Account::factory()->create(['name' => 'Gamma Inc', 'email' => 'gamma@example.com']);

        $this->actingAs($user);

        // Search by name
        $response = $this->getJson(route('fetchaccounts', ['q' => 'Alpha']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $account1->id, 'text' => 'Alpha Corp']);
        $response->assertJsonMissing(['id' => $account2->id, 'text' => 'Beta Ltd']);
        $response->assertJsonMissing(['id' => $account3->id, 'text' => 'Gamma Inc']);

        // Search by email
        $response = $this->getJson(route('fetchaccounts', ['q' => 'gamma@example.com']));
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $account3->id, 'text' => 'Gamma Inc']);
    }


    
    /** @test */
    public function it_sends_lead_email_and_logs_it()
    {
        Mail::fake(); // Fake sending emails

        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'to' => 'recipient@example.com',
            'cc' => 'cc1@example.com,cc2@example.com',
            'subject' => 'Test Lead Email',
            'body' => '<p>This is a test email body</p>',
            'leadid' => 1,
        ];

        $response = $this->postJson(route('sendleademail'), $payload);

        $response->assertStatus(200)
                ->assertJson(['success' => 'Email sent successfully.']);

        // Assert the email was queued
        Mail::assertQueued(LeadEmail::class, function ($mail) use ($payload) {
            return $mail->subjectLine === $payload['subject'] &&
                $mail->bodyText === $payload['body'];
        });

        // Assert database entry
        $this->assertDatabaseHas('email_logs', [
            'email' => $payload['to'],
            'cc' => $payload['cc'],
            'subject' => $payload['subject'],
            'body' => $payload['body'],
            'lead_id' => $payload['leadid'],
        ]);
    }



    /** @test */
    public function it_uploads_an_image_and_returns_link()
    {
        Storage::fake('public'); // Fake the storage

        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a fake image
        $file = UploadedFile::fake()->image('test_image.jpg');

        $response = $this->postJson(route('leaduploadimage'), [
            'file' => $file,
        ]);

        // Assert response
        $response->assertStatus(200);
        $response->assertJsonStructure(['link']);

        // Assert file was moved to correct path
        $uploadedFilename = time().'_'.$file->getClientOriginalName();

        $this->assertFileExists(public_path('uploads/email/' . $uploadedFilename));
    }


     /** @test */
    public function it_uploads_a_file_and_returns_link()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a fake file
        $file = UploadedFile::fake()->create('document.pdf', 100); // 100 KB

        // Make POST request
        $response = $this->postJson(route('leaduploadfile'), [
            'file' => $file,
        ]);

        // Assert response
        $response->assertStatus(200);
        $response->assertJsonStructure(['link']);

        // Check that the file actually exists in public/uploads/email
        $link = $response->json('link');
        $filename = basename($link);
        $this->assertFileExists(public_path('uploads/email/' . $filename));

        // Clean up the uploaded file after test
        if (File::exists(public_path('uploads/email/' . $filename))) {
            File::delete(public_path('uploads/email/' . $filename));
        }
    }


    /** @test */
    public function it_displays_create_call_view_without_lead()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('create.call'));

        $response->assertStatus(200)
                 ->assertViewIs('leads.create_call')
                 ->assertViewHas('lead', null);
    }

    /** @test */
    public function it_displays_create_call_view_with_lead()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('create.call', ['lead' => $lead->id]));

        $response->assertStatus(200)
                 ->assertViewIs('leads.create_call')
                 ->assertViewHas('lead', $lead);
    }

    

}
