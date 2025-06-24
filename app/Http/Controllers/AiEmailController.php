<?php

namespace App\Http\Controllers;

use App\Models\AiEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use DataTables;



class AiEmailController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => AiEmail::with('contact')->get()
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'contact_id' => 'required|exists:contacts,id',
                'sequence_id' => 'nullable|string',
                'content' => 'required|string',
                'subject' => 'required|string',
                'status' => 'in:Sent,Opened,Replied,Bounced',
                'opened_at' => 'nullable|date',
                'clicked_at' => 'nullable|date',
                'replied_at' => 'nullable|date',
            ]);

            $email = AiEmail::create($validated);

            return response()->json([
                'message' => 'Email created successfully',
                'data' => $email
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            Log::error('AI Email store failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $email = AiEmail::with('contact')->findOrFail($id);

            return response()->json([
                'data' => $email
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Email not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $email = AiEmail::findOrFail($id);

            $validated = $request->validate([
                'sequence_id' => 'nullable|string',
                'content' => 'sometimes|required|string',
                'subject' => 'sometimes|required|string',
                'status' => 'in:Sent,Opened,Replied,Bounced',
                'opened_at' => 'nullable|date',
                'clicked_at' => 'nullable|date',
                'replied_at' => 'nullable|date',
            ]);

            $email->update($validated);

            return response()->json([
                'message' => 'Email updated successfully',
                'data' => $email
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Email not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy($id)
    {
        try {
            $email = AiEmail::findOrFail($id);
            $email->delete();

            return response()->json([
                'message' => 'Email deleted successfully'
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'AI Email not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function aiemails(Request $request){


        if ($request->ajax()) {

            if (auth()->user()->user_role == 'super admin') {
                $data = AiEmail::select('leads.name as leadname','contacts.name as contactname','ai_emails.*')->leftjoin('leads','leads.id','ai_emails.lead_id')->leftjoin('contacts','contacts.id','ai_emails.contact_id');
            } else {
                $data = AiEmail::select('leads.name as leadname','contacts.name as contactname','ai_emails.*')->leftjoin('leads','leads.id','ai_emails.lead_id')->leftjoin('contacts','contacts.id','ai_emails.contact_id')->where('contacts.company_id', auth()->user()->company_id);
            }




            return Datatables::of($data)
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


        return view('ai.emails');
    }
}
