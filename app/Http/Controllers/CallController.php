<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Call;
use App\Models\Lead;
use Illuminate\Http\Request;
use DateTime;
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{

  public function history(Lead $lead, Call $call)
{
    if (!$call) {
        $call = Call::where('lead_id', $lead->id)
                  ->where('status', 'planned')
                  ->latest()
                  ->first();
    }

    return view('leads.callhistory', compact('lead', 'call'));
}

public function updateDetails(Request $request, Lead $lead)
{
    $validated = $request->validate([
        'transcript' => 'nullable|string',
        'sentiment' => 'nullable|in:positive,neutral,negative',
        'outcome' => 'nullable|string|max:255',
        'audio_link' => 'nullable|url',
        'call_id' => 'required|exists:calls,id'
    ]);

    $call = Call::find($request->call_id);

    if ($call && $call->lead_id == $lead->id) {
        $call->update([
            'transcript' => $validated['transcript'],
            'sentiment' => $validated['sentiment'],
            'outcome' => $validated['outcome'],
            'audio_link' => $validated['audio_link'],
            'status' => 'held'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call details saved successfully!',
            'redirect' => route('leads.show', $lead->id)
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Call not found or doesn\'t belong to this lead'
    ], 404);
}
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


    public function callScreen(Request $request)
    {
        
        $leadid= $request->leadid;
        $contact= $request->contact;

        $leadcontacts=[];

        if($contact ==''){
            $leadcontacts=Lead::find($leadid);
            $leadcontacts=$leadcontacts->contacts->pluck('phone');
        }

        return view('call_screen')->with('leadcontacts',$leadcontacts)->with('contact',$contact)->with('leadid',$leadid);
    }

   
    

    public function generateTwilioToken()
    {
        $accountSid = config('twilio.sid');
        $apiKeySid = config('twilio.api_key');
        $apiKeySecret = config('twilio.api_secret');
        $outgoingAppSid = config('twilio.twiml_app_sid');

        $identity = "agent_" . rand(1000, 9999);

        $token = new AccessToken($accountSid, $apiKeySid, $apiKeySecret, 3600, $identity);

        $voiceGrant = new VoiceGrant();
        $voiceGrant->setOutgoingApplicationSid($outgoingAppSid);
        $voiceGrant->setIncomingAllow(true);

        $token->addGrant($voiceGrant);

        return response()->json(['token' => $token->toJWT()]);
    }


    public function handleVoiceCall(Request $request)
    {
        $twiml = new \Twilio\TwiML\VoiceResponse();

        
        $to = $request->To;

        Log::info($to);

        if ($to) {
            $twiml->dial($to);
        } else {
            $twiml->say("Sorry, the number was invalid.");
        }

        return response($twiml)->header('Content-Type', 'text/xml');
    }



}