<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    public function sendToDevice(FirebaseService $firebase)
    {
        $deviceToken = 'f9qT9uLFQymtfxsrGConqq:APA91bHhGK1RGYbw2jy5hEKwq-wjiKLJzqBf6VIs2-xIZXU4GKimnOPWwkas02PvuhqJw-Bj6ebMh_ww2b7q8FSyW4l3AlyQKvQO6L_s-hdPUzhckrFmLZc';
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
