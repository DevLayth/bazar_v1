<?php

namespace App\Http\Controllers;
use App\Services\FirebaseService;

class FirebaseNotifyController extends Controller{
protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function sendPush()
    {
        $deviceToken = 'dzEwOvMzT72kg3NT4H2F9O:APA91bH4JAI2cJx8BMIJ1FvELClehDSGlBMhwtjqrLSObZXoSwbLgNKVX_KmSbA_XD3v-4bhB3j7-kga4mAUTYTUbOZKImKqKPH_kua7mzOaa9ba47MD9dM';
        $title = 'Hello';
        $body = 'This is a test message';

        $response = $this->firebase->sendNotification($deviceToken, $title, $body);

        return response()->json($response);
    }
}
