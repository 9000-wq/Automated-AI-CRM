<?php

namespace App\Http\Controllers;

use App\Models\AiCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use DataTables;

class AiCallController extends Controller
{
    public function index()
    {
        $calls = AiCall::with(['contact', 'lead'])->latest()->get();

        return response()->json([
            'data' => $calls
        ], Response::HTTP_OK);
    }
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'contact_id' => 'required|exists:contacts,id',
                'lead_id' => 'nullable|exists:leads,id',
                'direction' => 'required|in:Inbound,Outbound',
                'transcript' => 'nullable|string',
                'sentiment' => 'nullable|string',
                'outcome' => 'nullable|in:Converted,No Answer,Escalated',
                'audio_link' => 'nullable|url'
            ]);

            $call = AiCall::create($data);

            return response()->json([
                'message' => 'AI Call created successfully.',
                'data' => $call
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('Error creating AI Call: ' . $e->getMessage());

            return response()->json([
                'error' => 'Internal server error.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $call = AiCall::with(['contact', 'lead'])->findOrFail($id);

            return response()->json([
                'data' => $call
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Call not found.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $call = AiCall::findOrFail($id);

            $data = $request->validate([
                'contact_id' => 'required|exists:contacts,id',
                'lead_id' => 'nullable|exists:leads,id',
                'direction' => 'required|in:Inbound,Outbound',
                'transcript' => 'nullable|string',
                'sentiment' => 'nullable|string',
                'outcome' => 'nullable|in:Converted,No Answer,Escalated',
                'audio_link' => 'nullable|url'
            ]);

            $call->update($data);

            return response()->json([
                'message' => 'AI Call updated successfully.',
                'data' => $call
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Call not found.'
            ], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed.',
                'details' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $call = AiCall::findOrFail($id);
            $call->delete();

            return response()->json([
                'message' => 'AI Call deleted successfully.'
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Call not found.'
            ], Response::HTTP_NOT_FOUND);
        }
    }

   public function aicalls(Request $request)
{
    if ($request->ajax()) {
        $query = AiCall::select([
                'leads.name as leadname',
                'contacts.name as contactname',
                'ai_calls.*'  // Make sure this matches your table name
            ])
            ->leftJoin('leads', 'leads.id', '=', 'ai_calls.lead_id')
            ->leftJoin('contacts', 'contacts.id', '=', 'ai_calls.contact_id');

        if (auth()->user()->user_role != 'super admin') {
            $query->where('contacts.company_id', auth()->user()->company_id);
        }

        return Datatables::of($query)
            ->addIndexColumn()       
            ->filterColumn('contactname', function ($query, $keyword) {
                $query->where('contacts.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('leadname', function ($query, $keyword) {
                $query->where('leads.name', 'like', "%{$keyword}%");
            })
            ->rawColumns([])
            ->make(true);
    }

    return view('ai.calls');
}
}