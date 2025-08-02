<?php

namespace App\Services;

class FirebaseService
{
    protected $serverKey;

    public function __construct()
    {
        $this->serverKey = env('FIREBASE_SERVER_KEY'); // or paste the key directly for testing
    }

    public function sendNotification($deviceToken, $title, $body)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $notification = [
            'title' => $title,
            'body'  => $body,
        ];

        $fields = [
            'to' => $deviceToken,
            'notification' => $notification,
            'priority' => 'high',
        ];

        $headers = [
            'Authorization: key=' . $this->serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // set to false only for debugging
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        if ($result === FALSE) {
            return 'Curl failed: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($result, true);
    }
}
