<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPlanSubscription;


class ProfileController extends Controller
{
    public function index(){
        $profiles = Profile::all();
        return response()->json($profiles);
    }

    public function store(Request $request){
        $request->validate([
            'type' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'img' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address_1' => 'nullable|exists:addresses,id',
            'address_2' => 'nullable|exists:addresses,id',

        ]);

        $profile = Profile::create($request->all());
        return response()->json(['message' => 'Profile created successfully.', 'profile' => $profile]);
    }

    public function show($id){
        $profile = Profile::findOrFail($id);
        return response()->json($profile);
    }

    public function update(Request $request, $id){
        $profile = Profile::findOrFail($id);

        $request->validate([
            'type' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address_1' => 'nullable|exists:addresses,id',
            'address_2' => 'nullable|exists:addresses,id',


        ]);

        $profile->update($request->all());
        return response()->json(['message' => 'Profile updated successfully.', 'profile' => $profile]);
    }

    public function destroy($id){
        $profile = Profile::findOrFail($id);
        $profile->delete();
        return response()->json(['message' => 'Profile deleted successfully.']);
    }

    public function getProfileByUserId(Request $request) {

        $id= $request->user()->id;
        //subscription check
        $postCounter = null;
        $subscription = UserPlanSubscription::where('user_id', $id)
                ->latest()
                ->first();
        //if subscription expired
        if($subscription->created_at->addDays($subscription->plan->duration) < now()){
            $subscription->delete();
            $subscription = new UserPlanSubscription();
            $subscription->user_id = $id;
            $subscription->plan_id = 1;
            $postCounter = 0;
            $subscription->save();
        }
            $postCounter = [$subscription->plan->max_posts_per_month, $subscription->posts_counter];
        //end subscription check
        $profile = Profile::where('user_id', $id)->firstOrFail();
        return response()->json(['profile' => $profile, 'subscription' => $subscription , 'postCounter' => $postCounter]);
    }

    public function updateProfileByUserId(Request $request){
        $profile = Profile::where('user_id', $request->user()->id)->firstOrFail();
        $user = User::findOrFail($request->user()->id);

         $request->validate([
        'name' => 'nullable|string',
        'type' => 'nullable|string',
        'address' => 'nullable|string',
        'phone' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'address_1' => 'nullable|exists:addresses,id',
        'address_2' => 'nullable|exists:addresses,id',
     ]);

        if ($request->has('name')) {
        $user->name = $request->input('name');
        $user->save();
         }

         $profileData = $request->only(['type', 'address', 'phone', 'latitude', 'longitude','address_1','address_2']);
         $profile->update($profileData);

       return response()->json([
        'message' => 'Profile updated successfully.',
        'profile' => $profile,
        'user' => $user
         ]);
    }

    public function updateProfileImgByUserId(Request $request){
        $id= $request->user()->id;
        $profile = Profile::where('user_id', $id)->firstOrFail();

        $request->validate([
            'img' => 'nullable|string',
        ]);

        $profile->update($request->all());
        return response()->json(['message' => 'Profile updated successfully.', 'profile' => $profile]);
    }

    public function uploadProfileImg(Request $request)  {
        try {
            $request->validate([
                'image' => 'required|image',
            ]);

            $user = User::findOrFail($request->user()->id);
            $profile = Profile::where('user_id', $request->user()->id)->firstOrFail();

            if($profile->img != 'image.png'){
                 if ($profile->img && file_exists(public_path('images/profile') . '/' . $profile->img )) {
                unlink(public_path('images/profile') . '/' . $profile->img);
            }
            }

            $imageName = time() . '_' . $user->name . '.' . $request->image->extension();
            $request->image->move(public_path('images/profile'), $imageName);

            $profile->update(['img' => $imageName]);

            return response()->json(['success' => 'Image uploaded successfully.', 'image' => $imageName]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
