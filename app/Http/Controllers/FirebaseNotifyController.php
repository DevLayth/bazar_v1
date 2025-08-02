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
        $deviceToken = 'f9qT9uLFQymtfxsrGConqq:APA91bHhGK1RGYbw2jy5hEKwq-wjiKLJzqBf6VIs2-xIZXU4GKimnOPWwkas02PvuhqJw-Bj6ebMh_ww2b7q8FSyW4l3AlyQKvQO6L_s-hdPUzhckrFmLZc';
        $title = 'Hello';
        $body = 'This is a test message';

        $response = $this->firebase->sendNotification($deviceToken, $title, $body);

        return response()->json($response);
    }
}
