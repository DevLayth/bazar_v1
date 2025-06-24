<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // List all plans
    public function index()
    {
        $plans = Plan::all();
        return response()->json($plans);
    }

    // Show a single plan
    public function show($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        return response()->json($plan);
    }

    // Create a new plan
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'max_posts_per_month' => 'required|integer',
            'duration' => 'required|integer',
        ]);

        $plan = Plan::create($validatedData);

        return response()->json($plan, 201);
    }

    // Update an existing plan
    public function update(Request $request, $id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'max_posts_per_month' => 'sometimes|integer',
            'duration' => 'sometimes|integer',
        ]);

        $plan->update($validatedData);

        return response()->json($plan);
    }

    // Delete a plan
    public function destroy($id)
    {
        $plan = Plan::find($id);

        if (!$plan) {
            return response()->json(['message' => 'Plan not found'], 404);
        }

        $plan->delete();

        return response()->json(['message' => 'Plan deleted successfully']);
    }


  
}
