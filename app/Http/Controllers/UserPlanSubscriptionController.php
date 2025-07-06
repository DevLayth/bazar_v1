<?php

namespace App\Http\Controllers;

use App\Models\UserPlanSubscription;
use Illuminate\Http\Request;

class UserPlanSubscriptionController extends Controller
{
    /**
     * Return the plan_id based on user_id
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPlanByUserId($user_id) {
        $subscription = UserPlanSubscription::where('user_id', $user_id)->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found for this user'
            ], 404);
        }

        return
            ['plan_id' => $subscription->plan_id];

    }

    public function setPlanByUserId($user_id, $plan_id) {
        UserPlanSubscription::create([
            'user_id' => $user_id,
            'plan_id' => $plan_id
        ]);
    }


    //change plan by user_id
    public function changePlanByUserId($user_id, $plan_id) {
        $subscription = UserPlanSubscription::where('user_id', $user_id)->first();
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found for this user'
            ], 404);
        }

        $subscription->plan_id = $plan_id;
        $subscription->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan updated successfully'
        ],200);
    }
}
