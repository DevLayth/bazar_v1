<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
   public function storeOrUpdateToken(Request $request)
{
    $request->validate([
        'token' => 'required|string',
        'device_type' => 'nullable|string|in:android,ios,web',
    ]);

    $userId = Auth::id();

    if ($userId) {
        $updated = DeviceToken::where('token', $request->token)
            ->whereNull('user_id')
            ->update([
                'user_id' => $userId,
                'device_type' => $request->device_type,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            DeviceToken::updateOrCreate(
                ['token' => $request->token],
                [
                    'user_id' => $userId,
                    'device_type' => $request->device_type,
                ]
            );
        }
    } else {
        DeviceToken::firstOrCreate(
            ['token' => $request->token],
            [
                'user_id' => null,
                'device_type' => $request->device_type,
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
