<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function sendToDevice(FirebaseService $firebase)
    {
        $deviceToken = 'dzEwOvMzT72kg3NT4H2F9O:APA91bH4JAI2cJx8BMIJ1FvELClehDSGlBMhwtjqrLSObZXoSwbLgNKVX_KmSbA_XD3v-4bhB3j7-kga4mAUTYTUbOZKImKqKPH_kua7mzOaa9ba47MD9dM';
        $title = 'New Message';
        $body = 'You have a new notification.';
        $imgURL = 'https://bazzarv1.newstepiq.com/images/image.png';

        try {
            $firebase->sendNotification($deviceToken, $title, $body,$imgURL);
            return response()->json(['message' => 'Notification sent']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
