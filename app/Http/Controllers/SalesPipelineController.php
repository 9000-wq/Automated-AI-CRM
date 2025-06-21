<?php

namespace App\Http\Controllers;

use App\Models\SalesPipeline;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SalesPipelineController extends Controller
{
    public function index()
    {
        $pipelines = SalesPipeline::with('lead')->latest()->get();

        return response()->json(['data' => $pipelines], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'stage' => 'required|in:Created,Contacted,Proposal Sent,Closed',
                'probability' => 'nullable|integer|min:0|max:100',
                'ai_notes' => 'nullable|string',
                'outcome' => 'nullable|in:Won,Lost,Escalated',
                'closed_at' => 'nullable|date',
            ]);

            $pipeline = SalesPipeline::create($data);

            return response()->json(['message' => 'Created successfully', 'data' => $pipeline], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Store Pipeline Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $pipeline = SalesPipeline::with('lead')->findOrFail($id);
            return response()->json(['data' => $pipeline], Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Pipeline not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pipeline = SalesPipeline::findOrFail($id);

            $data = $request->validate([
                'stage' => 'sometimes|in:Created,Contacted,Proposal Sent,Closed',
                'probability' => 'nullable|integer|min:0|max:100',
                'ai_notes' => 'nullable|string',
                'outcome' => 'nullable|in:Won,Lost,Escalated',
                'closed_at' => 'nullable|date',
            ]);

            $pipeline->update($data);

            return response()->json(['message' => 'Updated', 'data' => $pipeline], Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Pipeline not found'], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            SalesPipeline::findOrFail($id)->delete();
            return response()->json(['message' => 'Deleted'], Response::HTTP_OK);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Pipeline not found'], Response::HTTP_NOT_FOUND);
        }
    }
}

