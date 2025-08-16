<?php

namespace App\Http\Controllers;

use App\Models\UserPlanSubscription;
use Illuminate\Http\Request;

class UserPlanSubscriptionController extends Controller
{
    const DEFAULT_PLAN_ID = 1;

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

    public function checkExpiredSubscriptions() {
        $subscriptions = UserPlanSubscription::with('plan')->get();

        foreach ($subscriptions as $subscription) {
            $duration = $subscription->plan->duration;

            if ($subscription->created_at < now()->subDays($duration)) {
                $subscription->plan_id = 1;
                $subscription->created_at = now();
                $subscription->updated_at = now();
                $subscription->posts_counter = 0;
                $subscription->save();
            }
        }
    }
}
