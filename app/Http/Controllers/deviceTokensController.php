<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class deviceTokensController extends Controller
{
    // index method to get all device tokens
    public function getAll()
    {
        $deviceTokens = DeviceToken::all();
        return response()->json($deviceTokens);
    }

public function storeOrUpdateToken(Request $request)
{
    $validated = $request->validate([
        'token' => ['required', 'string'],
        'device_type' => ['nullable', 'string', 'in:android,ios,web'],
        'language' => ['required', 'integer', 'between:0,255'],
    ]);

    $userId = Auth::id();

    // Check if the token already exists
    $deviceToken = DeviceToken::where('token', $validated['token'])->first();

    if ($deviceToken) {
        // Update existing token
        $deviceToken->update([
            'user_id' => $userId,
            'device_type' => $validated['device_type'] ?? null,
            'language' => $validated['language'],
            'updated_at' => now(),
        ]);
    } else {
        // Create new token
        DeviceToken::create([
            'token' => $validated['token'],
            'user_id' => $userId,
            'device_type' => $validated['device_type'] ?? null,
            'language' => $validated['language'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }



    return response()->json([
        'message' => 'Device token saved or updated successfully.',
    ], 200);
}

}
