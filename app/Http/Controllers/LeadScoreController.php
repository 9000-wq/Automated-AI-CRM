<?php

namespace App\Http\Controllers;

use App\Models\LeadScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

class LeadScoreController extends Controller
{
    public function index()
    {
        return response()->json(LeadScore::with(['lead', 'contact'])->get());
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'contact_id' => 'required|exists:contacts,id',
                'profile_score' => 'required|integer',
                'engagement_score' => 'required|integer',
                'intent_score' => 'required|integer',
                'external_data_score' => 'required|integer',
                'total_score' => 'required|integer',
                'score_band' => 'required|in:Cold,Warm,Hot,Very Hot',
                'next_best_action' => 'nullable|string',
                'last_scored_at' => 'nullable|date_format:Y-m-d H:i:s',
                'explanation' => 'nullable|array',
            ]);

            $leadScore = LeadScore::create($data);

            return response()->json($leadScore, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            Log::error("LeadScore Store Error: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show($lead_id, $contact_id)
    {
        return response()->json(
            LeadScore::where('lead_id', $lead_id)
                ->where('contact_id', $contact_id)
                ->with(['lead', 'contact'])
                ->firstOrFail()
        );
    }

    public function update(Request $request, $lead_id, $contact_id)
    {
        try {
            $leadScore = LeadScore::where('lead_id', $lead_id)
                ->where('contact_id', $contact_id)
                ->firstOrFail();

            $data = $request->validate([
                'profile_score' => 'sometimes|required|integer',
                'engagement_score' => 'sometimes|required|integer',
                'intent_score' => 'sometimes|required|integer',
                'external_data_score' => 'sometimes|required|integer',
                'total_score' => 'sometimes|required|integer',
                'score_band' => 'sometimes|required|in:Cold,Warm,Hot,Very Hot',
                'next_best_action' => 'nullable|string',
                'last_scored_at' => 'nullable|date_format:Y-m-d H:i:s',
                'explanation' => 'nullable|array',
            ]);

            $leadScore->update($data);
            return response()->json($leadScore);
        } catch (\Throwable $e) {
            Log::error("LeadScore Update Error: " . $e->getMessage());
            return response()->json(['error' => 'Update failed'], 500);
        }
    }

    public function destroy($lead_id, $contact_id)
    {
        LeadScore::where('lead_id', $lead_id)
            ->where('contact_id', $contact_id)
            ->delete();

        return response()->json(['message' => 'Deleted']);
    }
}

