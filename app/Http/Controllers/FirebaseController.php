<?php

namespace App\Http\Controllers;

use App\Models\DeviceToken;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FirebaseController extends Controller
{
    public function sendToDevice(FirebaseService $firebase)
    {
        $deviceToken = 'dzEwOvMzT72kg3NT4H2F9O:APA91bH4JAI2cJx8BMIJ1FvELClehDSGlBMhwtjqrLSObZXoSwbLgNKVX_KmSbA_XD3v-4bhB3j7-kga4mAUTYTUbOZKImKqKPH_kua7mzOaa9ba47MD9dM';
        $title = 'New Message';
        $body = 'You have a new notification.';
        $imgURL = 'https://bazzarv1.newstepiq.com/images/image.png';

        try {
            $firebase->sendNotification($deviceToken, $title, $body, $imgURL);
            return response()->json(['message' => 'Notification sent']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

   public function sendToMultipleDevices(Request $request, FirebaseService $firebase)
{
    $target = $request->input('target');
    $titles = [
        1 => $request->input('en_title', 'Default EN Title'),
        2 => $request->input('ar_title', 'Default AR Title'),
        3 => $request->input('ku_title', 'Default KU Title'),
    ];
    $bodies = [
        1 => $request->input('en_body', 'Default EN Body'),
        2 => $request->input('ar_body', 'Default AR Body'),
        3 => $request->input('ku_body', 'Default KU Body'),
    ];
    $imgURL = $request->input('img_url', 'https://bazzarv1.newstepiq.com/images/image.png');

    $devices = collect();

    if ($target == 1) {
        $devices = DB::table('device_tokens')
            ->join('users', 'device_tokens.user_id', '=', 'users.id')
            ->where('users.admin', 1)
            ->select('device_tokens.user_id', 'device_tokens.token', 'device_tokens.language')
            ->get();
    } elseif ($target == 2) {
        $devices = DB::table('device_tokens')
            ->join('users', 'device_tokens.user_id', '=', 'users.id')
            ->where('users.admin', 0)
            ->select('device_tokens.user_id', 'device_tokens.token', 'device_tokens.language')
            ->get();
    } elseif ($target == 3) {
        $devices = DB::table('device_tokens')
            ->whereNull('user_id')
            ->select(DB::raw('NULL as user_id'), 'token', 'language')
            ->get();
    } else {
        return response()->json(['error' => 'Invalid target'], 400);
    }

    $successfulUserIds = [];
    $failed = [];

    foreach ($devices as $device) {
        try {
            $language = $device->language ?? 1; // default to EN if null
            $title = $titles[$language] ?? $titles[1];
            $body = $bodies[$language] ?? $bodies[1];

            $firebase->sendNotification($device->token, $title, $body, $imgURL);

            if (!is_null($device->user_id)) {
                $successfulUserIds[] = $device->user_id;
            }
        } catch (\Throwable $e) {
            $failed[] = [
                'user_id' => $device->user_id,
                'token' => $device->token,
                'error' => $e->getMessage()
            ];
        }
    }

    return response()->json([
        'message' => 'Notifications sent',
        'notified_user_ids' => array_values(array_unique($successfulUserIds)),
        'failed' => $failed
    ]);
}


}
