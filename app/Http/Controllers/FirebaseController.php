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

    // send notification to multiple devices tokens from database admin users only
    public function sendToMultipleDevices(Request $request, FirebaseService $firebase)
    {
        $target = $request->input('target');
        if($target ==1){
            $adminDevices = DB::table('device_tokens')
            ->join('users', 'device_tokens.user_id', '=', 'users.id')
            ->where('users.admin', 1)
            ->select('device_tokens.user_id', 'device_tokens.token')
            ->get();

        $title = $request->input('title', 'Default Title');
        $body = $request->input('body', 'Default Body');
        $imgURL = $request->input('img_url', 'https://bazzarv1.newstepiq.com/images/image.png');

        $successfulUserIds = [];
        $failed = [];

        foreach ($adminDevices as $device) {
            try {
                $firebase->sendNotification($device->token, $title, $body, $imgURL);
                $successfulUserIds[] = $device->user_id;
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
            'notified_user_ids' => $successfulUserIds,
            'failed' => $failed
        ]);
        // If target is 2, send to all users devices
        }else if($target == 2){
            $userDevices = DB::table('device_tokens')
            ->join('users', 'device_tokens.user_id', '=', 'users.id')
            ->where('users.admin', 0)
            ->select('device_tokens.user_id', 'device_tokens.token')
            ->get();

        $title = $request->input('title', 'Default Title');
        $body = $request->input('body', 'Default Body');
        $imgURL = $request->input('img_url', 'https://bazzarv1.newstepiq.com/images/image.png');

        $successfulUserIds = [];
        $failed = [];

        foreach ($userDevices as $device) {
            try {
                $firebase->sendNotification($device->token, $title, $body, $imgURL);
                $successfulUserIds[] = $device->user_id;
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
            'notified_user_ids' => $successfulUserIds,
            'failed' => $failed
        ]);

        // If target is 3, send to guest devices
        }else if ($target == 3) {
           $guestDevices = DB::table('device_tokens')
            ->whereNull('user_id')
            ->select('token')
            ->get();


        $title = $request->input('title', 'Default Title');
        $body = $request->input('body', 'Default Body');
        $imgURL = $request->input('img_url', 'https://bazzarv1.newstepiq.com/images/image.png');

        $successfulUserIds = [];
        $failed = [];

        foreach ($guestDevices as $device) {
            try {
                $firebase->sendNotification($device->token, $title, $body, $imgURL);
                $successfulUserIds[] = $device->user_id;
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
            'notified_user_ids' => $successfulUserIds,
            'failed' => $failed
        ]);
        }
}
}
