<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Models\Lead; // Added Lead model import
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    public function showEmailLogs()
    {
       
        return view('leads.emailindex'); 
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'emailTo' => 'required|email',
            'emailSubject' => 'required',
            'emailBody' => 'required'
        ]);

        $log = EmailLog::create([
            'lead_id' => $request->emailParent ?: null,
            'email' => $request->emailTo,
            'cc' => $request->emailCC,
            'subject' => $request->emailSubject,
            'body' => $request->emailBody,
            'status' => 'sent'
        ]);

        // Here you would add your actual email sending logic
        // Mail::to($request->emailTo)->send(new CustomEmail($log));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully'
        ]);
    }

    public function getEmailLogsData(Request $request)
    {
        $logs = EmailLog::with('lead') 
            ->when($request->lead_id, function($query) use ($request) {
                $query->where('lead_id', $request->lead_id);
            })
            ->orderBy('created_at', 'desc');

        return datatables()->of($logs)
            ->addColumn('parent', function($log) {
                return $log->lead ? $log->lead->name : 'N/A';
            })
            ->editColumn('body', function($log) {
                return Str::limit(strip_tags($log->body), 100);
            })
            ->addColumn('action', function($log) {
                return '<button class="btn btn-sm btn-primary view-log" data-id="'.$log->id.'">View</button>';
            })
            ->rawColumns(['body', 'action'])
            ->make(true);
    }

    public function getEmailLog($id)
    {
        $log = EmailLog::findOrFail($id);
        return response()->json([
            'subject' => $log->subject,
            'body' => $log->body,
            'to' => $log->email,
            'cc' => $log->cc,
            'parent' => $log->lead ? $log->lead->name : null
        ]);
    }

    public function getLeads(Request $request)
    {

        $search = $request->input('term');

        $leads = Lead::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20, ['id', 'name']);

        return response()->json([
            'data' => $leads->items(),
            'total' => $leads->total()
        ]);
    }
}