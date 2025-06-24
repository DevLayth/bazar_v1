<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Infobip\Configuration;
use Infobip\ApiException;
use Infobip\Api\SmsApi;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
class SendSMSController extends Controller
{
    public function sendSMS(Request $request){
        $configuration = new Configuration(
            host: 'e1zjgq.api.infobip.com',
            apiKey: '66072c07cb0578a38645959b6e6c920a-533c2378-46b6-4a36-bd0c-eb3eb2c62c62'
        );

        $sendSmsApi = new SmsApi(config: $configuration);

        $message = new SmsTextualMessage(
            destinations: [
                new SmsDestination(to: $request->number)
            ],
            from: 'Bazaar',
            text: $request->message
        );

        $request = new SmsAdvancedTextualRequest(messages: [$message]);

        try {
            $smsResponse = $sendSmsApi->sendSmsMessage($request);
            return redirect('/send-sms')->with('success','SMS was sent successfully');
        } catch (ApiException $apiException) {
            return redirect('/send-sms')->with('fail',$apiException->getMessage());
        }
    }
}
