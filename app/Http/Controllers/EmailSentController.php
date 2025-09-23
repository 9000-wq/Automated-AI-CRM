<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SentEmail;

class EmailSentController extends Controller
{
    public function sent(Request $request)
    {
        // If DataTables AJAX request → return JSON
        if ($request->ajax()) {
            $query = SentEmail::query();

            // Total records count
            $totalRecords = $query->count();

            // Search filter
            if ($request->has('search') && !empty($request->search['value'])) {
                $search = $request->search['value'];
                $query->where(function($q) use ($search) {
                    $q->where('to', 'like', "%{$search}%")
                      ->orWhere('cc', 'like', "%{$search}%")
                      ->orWhere('subject', 'like', "%{$search}%")
                      ->orWhere('body', 'like', "%{$search}%");
                });
            }

            $filteredRecords = $query->count();

            // Sorting
            $columns = ['to', 'cc', 'subject', 'body'];
            if ($request->has('order')) {
                $orderColIndex = $request->order[0]['column'];
                $orderDir = $request->order[0]['dir'];
                if (isset($columns[$orderColIndex])) {
                    $query->orderBy($columns[$orderColIndex], $orderDir);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination (start & length from DataTables)
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $emails = $query->skip($start)->take($length)->get();

            // Return response in DataTables format
            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $emails,
            ]);
        }

        // Non-AJAX request → just return view
        return view('EmailSent');
    }
}
