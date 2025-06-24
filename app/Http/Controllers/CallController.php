<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Http\Request;
use DateTime;

class CallController extends Controller
{
    public function create($lead = null)
    {
        $parentData = null;
        
        if ($lead) {
            $leadRecord = Lead::find($lead);
            if ($leadRecord) {
                $parentData = [
                    'type' => 'Lead',
                    'name' => $leadRecord->name,
                    'id' => $leadRecord->id
                ];
            }
        }

        return view('leads.create_call', compact('parentData'));
    }

    public function store(Request $request, $lead = null)
    {
        $request->validate([
            'name' => 'required',
            'date_start' => 'required',
            'time_start' => 'required',
            'date_end' => 'required',
            'time_end' => 'required',
            'duration' => 'required'
        ]);

        try {
            $startDateTime = DateTime::createFromFormat('d.m.Y H:i', $request->date_start . ' ' . $request->time_start);
            $endDateTime = DateTime::createFromFormat('d.m.Y H:i', $request->date_end . ' ' . $request->time_end);
            
            if (!$startDateTime || !$endDateTime) {
                throw new \Exception('Invalid date/time format');
            }

            $callData = [
                'name' => $request->name,
                'status' => $request->status ?? 'Planned',
                'direction' => $request->direction ?? 'Outbound',
                'date_start' => $startDateTime->format('Y-m-d H:i:s'),
                'date_end' => $endDateTime->format('Y-m-d H:i:s'),
                'duration' => $request->duration,
                'parent_type' => $request->parent_type,
                'parent_name' => $request->parent_name,
                'description' => $request->description,
                'assigned_user_name' => $request->assigned_user_name,
                'teams' => $request->teams,
                'users' => $request->users,
                'contacts' => $request->contacts,
                'leads' => $request->leads,
                'lead_id' => $lead ?: null,
                'company_id' => Auth::user()->company_id,
            ];

            $call = Call::create($callData);

            return response()->json([
                'success' => true,
                'message' => 'Call saved successfully!',
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving call: ' . $e->getMessage()
            ], 500);
        }
    }

  public function getCallsByLead($leadId)
{
    // Activities: Planned or In Progress calls
    $activities = Call::where('lead_id', $leadId)
                    ->whereIn('status', ['planned']) 
                    ->orderBy('date_start', 'desc')
                    ->get();

    // History: Completed/Held calls
    $history = Call::where('lead_id', $leadId)
                 ->where('status', 'held') 
                 ->orderBy('date_start', 'desc')
                 ->get();

    return response()->json([
        'activities' => $activities,
        'history' => $history
    ]);
}
}