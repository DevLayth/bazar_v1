<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokensController extends Controller
{
    // index method to get all device tokens
    public function getAll()
    {
        $deviceTokens = DeviceToken::all();
        return response()->json($deviceTokens);
    }

   public function storeOrUpdateToken(Request $request)
{
    // Validate incoming request
    $validated = $request->validate([
        'token' => ['required', 'string'],
        'device_type' => ['nullable', 'string', 'in:android,ios,web'],
        'language' => ['required', 'integer', 'between:0,255'],
    ]);

    $userId = Auth::id();

    if ($userId) {
        // Try to claim an existing token without user_id
        $updated = DeviceToken::where('token', $validated['token'])
            ->whereNull('user_id')
            ->update([
                'user_id' => $userId,
                'device_type' => $validated['device_type'] ?? null,
                'language' => $validated['language'],
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            // Either no such token or already assigned: update or create user token
            DeviceToken::updateOrCreate(
                [
                    'token' => $validated['token'],
                    'user_id' => $userId,
                ],
                [
                    'device_type' => $validated['device_type'] ?? null,
                    'language' => $validated['language'],
                    'updated_at' => now(),
                ]
            );
        }
    } else {
        // Guest device (no user_id)
        DeviceToken::firstOrCreate(
            [
                'token' => $validated['token'],
                'user_id' => null,
                'device_type' => $validated['device_type'] ?? null,
            ],
            [
                'language' => $validated['language'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    return response()->json([
        'message' => 'Device token saved or updated successfully.',
    ], 200);
}




    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $deleted = DeviceToken::where('token', $request->token)->delete();

        return response()->json([
            'message' => $deleted ? 'Token removed.' : 'Token not found.',
        ]);
    }
}
