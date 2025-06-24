<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\UserPlanSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use AuthorizesRequests;

    //all posts that are not pending
    public function index()
    {
        try {
            $posts = Post::with(['user.profile'])->latest()->where('pending', false)->get()->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user->id,
                    'user_name' => $post->user->name,
                    'user_image' => $post->user->profile->img,
                    'user_phone' => $post->user->profile->phone,
                    'images' => $post->images,
                    'title' => $post->title,
                    'body' => $post->body,
                    'price' => $post->price,
                    'pending' => $post->pending,
                    'currency' => $post->currency,
                    'category_id' => $post->category_id,
                    'created_at' => $post->created_at,
                ];
            });

            return response()->json([
                'message' => 'All posts retrieved successfully',
                'data' => $posts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts.',
            ], 500);
        }
    }

    public function adminIndex()
    {
        try {
            $posts = Post::with(['user.profile'])->latest()->get()->map(function ($post) {
                return [
                    'id' => $post->id,
                    'user_id' => $post->user->id,
                    'user_name' => $post->user->name,
                    'user_image' => $post->user->profile->img,
                    'user_phone' => $post->user->profile->phone,
                    'images' => $post->images,
                    'title' => $post->title,
                    'body' => $post->body,
                    'price' => $post->price,
                    'pending' => $post->pending,
                    'approved_by' => $post->approved_by->name ?? null,
                    'currency' => $post->currency,
                    'category_id' => $post->category_id,
                    'created_at' => $post->created_at,
                ];
            });

            return response()->json([
                'message' => 'All posts retrieved successfully',
                'data' => $posts,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching posts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
       try {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|integer|in:0,1', // 0 = IQD, 1 = USD
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'image',
        ]);

        $user = Auth::user();

        // -------- Subscription check --------
        $subscription = UserPlanSubscription::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'You need to subscribe to a plan to post.',
            ], 403);
        }


        $originalCreatedAt = $subscription->created_at;
        $expirationDate = Carbon::parse($originalCreatedAt)->addDays($subscription->plan->duration);

        // Renew free plan if expired
        if (Carbon::now()->greaterThan($expirationDate)) {
            if ($subscription->plan_id == 1) { // Free plan
                $subscription->delete();
                UserPlanSubscription::create([
                    'user_id' => $user->id,
                    'plan_id' => 1,
                    'posts_counter' => 0,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Your subscription has expired. Please renew to continue posting.',
                ], 403);
            }
        }

        // Check post limit
        if ($subscription->posts_counter >= $subscription->plan->max_posts_per_month) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your post limit for this month.',
            ], 403);
        }
        $pending=0;
        if($subscription->plan_id == 1) {
           $pending=1;
        }
        // -------- Image Upload --------
        if (!$request->hasFile('images')) {
            return response()->json([
                'success' => false,
                'message' => 'No images uploaded.',
            ], 400);
        }

        $imagePaths = [];
        $postDir = 'images/posts/' . $user->id . '/' . uniqid();

        foreach ($request->file('images') as $image) {
            if (!$image->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file.',
                ], 400);
            }

            $imageName = uniqid() . '.' . $image->extension();
            $destinationPath = public_path($postDir);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $image->move($destinationPath, $imageName);
            $imagePaths[] = $postDir . '/' . $imageName;
        }

        // -------- Create Post --------
        $post = Post::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'body' => $request->body,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'currency' => $request->currency,
            'images' => json_encode($imagePaths),
            'pending' => $pending,
        ]);

        // Increment post count
        $subscription->increment('posts_counter');
        if($pending){
              return response()->json([
            'success' => true,
            'message' => 'Post created successfully. Waiting for approval.',
            'data' => $post,
        ]);
        }else{
            return response()->json([
            'success' => true,
            'message' => 'Post created successfully.',
            'data' => $post,
        ]);
        }

    } catch (\Exception $e) {
        Log::error('Error creating post: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to create post.',
        ], 500);
    }
    }

    public function show(Post $post)
    {
        try {
            $this->authorize('view', $post);

            return response()->json([
                'success' => true,
                'data' => $post,
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing post: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post details.',
            ], 500);
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            $this->authorize('modify', $post);

            $request->validate([
                'title' => 'nullable|string|max:255',
                'body' => 'nullable|string',
                'price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|integer|in:0,1',
                'category_id' => 'nullable|exists:categories,id',
            ]);

            $dataToUpdate = $request->only(['title', 'body', 'price', 'currency', 'category_id']);

            $post->update($dataToUpdate);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully.',
                'data' => $post,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating post: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update post.',
            ], 500);
        }
    }

    public function destroy(Post $post)
    {
        try {
            $this->authorize('delete', $post);
            $images = json_decode($post->images, true);

            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    $filePath = public_path($imagePath);

                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                $directory = dirname(public_path($images[0]));

                if (is_dir($directory) && count(scandir($directory)) === 2) {
                    rmdir($directory);
                }
            }
            $post->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting post: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Item.',
            ], 500);
        }
    }

    public function userPosts()
    {
        try {
            $user = Auth::user();

            $posts = Post::where('user_id', $user->id)->latest()->get();

            return response()->json(
                $posts,
            );
        } catch (\Exception $e) {
            Log::error('Error fetching user posts: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user posts.',
            ], 500);
        }
    }


    // approval post
public function approvePost(Request $request, Post $post)
{
    try {
        $post->update([
            'pending' => 0,
            'approved_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post approved successfully.',
            'data' => $post,
        ]);
    } catch (\Exception $e) {
        Log::error('Error approving post: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to approve post.',
        ], 500);
    }
}



}
