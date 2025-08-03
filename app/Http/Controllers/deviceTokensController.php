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
    $request->validate([
        'token' => 'required|string',
        'device_type' => 'nullable|string|in:android,ios,web',
        'language' => 'nullable|integer',
    ]);

    $userId = Auth::id();

    if ($userId) {
        // Try to assign this token to the current user if it's not assigned yet
        $updated = DeviceToken::where('token', $request->token)
            ->whereNull('user_id')
            ->update([
                'user_id' => $userId,
                'device_type' => $request->device_type,
                'language' => $request->language,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            // No unclaimed token was updated, create or update a record with user_id
            DeviceToken::updateOrCreate(
                [
                    'token' => $request->token,
                    'user_id' => $userId,
                ],
                [
                    'device_type' => $request->device_type,
                    'language' => $request->language,
                    'updated_at' => now(),
                ]
            );
        }
    } else {
        // Guest user, store token without user_id
        DeviceToken::firstOrCreate(
            [
                'token' => $request->token,
                'user_id' => null,
                'language' => $request->language,
                'device_type' => $request->device_type,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    return response()->json([
        'message' => 'Device token saved or updated successfully.',
    ]);
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
