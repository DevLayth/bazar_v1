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
    $validated = $request->validate([
        'token' => ['required', 'string'],
        'device_type' => ['nullable', 'string', 'in:android,ios,web'],
        'language' => ['required', 'integer', 'between:0,255'],
    ]);

    $userId = Auth::id();

    DeviceToken::updateOrCreate(
        ['token' => $validated['token']],
        [
            'user_id' => $userId,
            'device_type' => $validated['device_type'] ?? null,
            'language' => $validated['language'],
            'updated_at' => now(),
        ]
    );

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
