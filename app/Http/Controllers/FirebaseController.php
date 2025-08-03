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
        'en' => $request->input('en_title', 'Default English Title'),
        'ar' => $request->input('ar_title', 'Default Arabic Title'),
        'ku' => $request->input('ku_title', 'Default Kurdish Title'),
    ];
    $bodies = [
        'en' => $request->input('en_body', 'Default English Body'),
        'ar' => $request->input('ar_body', 'Default Arabic Body'),
        'ku' => $request->input('ku_body', 'Default Kurdish Body'),
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
            $lang = in_array($device->language, ['en', 'ar', 'ku']) ? $device->language : 'en';

            $title = $titles[$lang] ?? $titles['en'];
            $body = $bodies[$lang] ?? $bodies['en'];

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
