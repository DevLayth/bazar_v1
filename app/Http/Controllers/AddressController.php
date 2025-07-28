<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    // Return all cities with their children (areas)
    public function getCitiesWithAreas()
    {
        $cities = Address::whereNull('parent_id')->with('children')->get();
        return response()->json($cities);
    }

    // store a new address
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ku' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:addresses,id',
        ]);

        $address = Address::create($request->all());
        return response()->json($address, 201);
    }

    // Update an existing address
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);
        $address->update($request->all());
        return response()->json($address, 200);
    }

    // Delete an address
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json(['success' => true], 204);
    }
}
