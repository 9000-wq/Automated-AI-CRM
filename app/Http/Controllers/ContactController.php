<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactRole;
use App\Models\Lead;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function getContactsByLead($lead_id)
    {
        // Validate lead_id exists
        $lead = Lead::find($lead_id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }

        // Get contacts for the lead with role relationship loaded
        $contacts = Contact::with('role')->where('lead_id', $lead_id)->get();

        return response()->json(['contacts' => $contacts]);
    }

    // Get all contacts
    public function index()
    {
        $contacts = Contact::with(['role', 'lead'])->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    // Store a new contact
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'lead_id'          => 'required|exists:leads,id',
            'contact_role_id'  => 'required|exists:contact_roles,id',
        ]);

        $contact = Contact::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'data' => $contact
        ], 201);
    }

    // Get a single contact
    public function show(Contact $contact)
    {
        $contact->load(['role', 'lead']);
        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    // Update a contact
    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'nullable|email|max:255',
            'birthday'         => 'nullable|date',
            'phone'            => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'lead_id'          => 'required|exists:leads,id',
            'contact_role_id'  => 'required|exists:contact_roles,id',
        ]);

        $contact->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    // Delete a contact
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }
}
