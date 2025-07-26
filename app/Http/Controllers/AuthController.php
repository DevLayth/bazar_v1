<?php

namespace App\Http\Controllers;

use App\Http\Controllers\UserPlanSubscriptionController;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $existingUser = User::where('email', $fields['email'])->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Email already exists'
            ], 400);
        }

        $user = User::create($fields);
        $user->sendEmailVerificationNotification();

        $profile = Profile::create([
            'user_id' => $user->id,
            'type' => null,
            'address' => $request->address ?? null,
            'phone' => $request->phone ?? null,
            'img' => 'image.png',
            'latitude' => $request->latitude ?? null,
            'longitude' => $request->longitude ?? null,
        ]);

        $token = $user->createToken($request->name);
        // Create a new UserPlanSubscriptionController instance
        $userPlanSubscriptionController = new UserPlanSubscriptionController();
        // Set the default plan for the user
        $userPlanSubscriptionController->setPlanByUserId($user->id, 1);
        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token->plainTextToken
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('admin', false)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'messageEN' => 'The provided credentials are incorrect.',
                'messageAR' => 'خطأ في تسجيل الدخول',
                'messageKU' => 'ئەڤ ئیمەیلە یێ خەلەتە'
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Your email address is not verified.'
            ], 401);
        }

        $token = $user->createToken($user->name)->plainTextToken;
        $profile = $user->profile;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

public function adminLogin(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:6',
    ]);

    // Check if the user exists and is an admin
    $user = User::where('email', $request->email)
        ->where('admin', true)
        ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'The provided credentials are incorrect.'
        ], 401);
    }

    // Ensure the email is verified
    if (!$user->hasVerifiedEmail()) {
        return response()->json([
            'success' => false,
            'message' => 'Your email address is not verified.'
        ], 401);
    }


    $token = $user->createToken($user->name . '_admin_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Login successful.',
        'user' => $user,
        'token' => $token
    ], 200);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'You are logged out'
        ]);
    }

    // delete user by id

    public function deleteUser($id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            if ($user->profile && $user->profile->img !== 'image.png') {
                $imagePath = public_path('images/profile/' . $user->profile->img);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $postDir = public_path('images/posts/' . $user->id);
            if (File::isDirectory($postDir)) {
                File::deleteDirectory($postDir);
            }

            $user->tokens()->delete();
            $user->delete();

            DB::commit();

            return response()->json(['message' => 'User deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete user', 'details' => $e->getMessage()], 500);
        }
    }

    public function getAllUsers()
    {
        $users = User::with(['profile', 'planSubscriptions'])->get();

        return $users->map(function ($user) {
            $planId = $user->planSubscriptions->sortByDesc('created_at')->first()->pivot->plan_id ?? null;
            $posts = $user->planSubscriptions->sortByDesc('created_at')->first()->pivot->posts_counter ?? null;
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,
                'img' => $user->profile->img,
                'phone' => $user->profile->phone,
                'plan_id' => $planId,
                'posts_counter' => $posts
            ];
        });
    }

    public function getAllStores()
    {
        $users = User::with(['profile', 'planSubscriptions'])
            ->where('admin', 0)->where('email_verified_at', '!=', null)
            ->get();

        return $users->map(function ($user) {
            $planSubscription = $user->planSubscriptions->sortByDesc('created_at')->first();
            $planId = $planSubscription->pivot->plan_id ?? null;
            $posts = $planSubscription->pivot->posts_counter ?? null;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'img' => $user->profile->img ?? null,
                'phone' => $user->profile->phone ?? null,
                'plan_id' => $planId,
                'address' => $user->profile->address,
                'posts_counter' => $posts,
                'created_at' => $user->created_at,
                'email_verified_at' => $user->email_verified_at,

            ];
        });
    }
}
