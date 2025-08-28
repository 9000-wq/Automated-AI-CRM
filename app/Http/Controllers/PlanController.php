<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{

    public function index()
    {
        $plans = Plan::all();
        return view('plans.index', compact('plans'));
    }

    public function create()
    {
        return view('plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|string',

        ]);

        $validated['features'] = isset($validated['features'])
        ? implode(',', array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features'])))
        : '';

        Plan::create($validated);

        return redirect()->route('plans.index')->with('success', 'Plan created successfully.');
    }

    public function show(Plan $plan)
    {
        return view('plans.show', compact('plan'));
    }

    public function edit(Plan $plan=null)
    {
        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'required|string',
        ]);

        $validated['features'] = isset($validated['features'])
        ? implode(',', array_map('trim', preg_split('/\r\n|\r|\n/', $validated['features'])))
        : '';

        $plan->update($validated);

        return redirect()->route('plans.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Request $req)
    {
        $plan = Plan::find($req->planid);

        if ($plan) {
            $plan->delete();
        }

        return redirect()->route('plans.index')->with('success', 'Plan deleted successfully.');
    }


}
