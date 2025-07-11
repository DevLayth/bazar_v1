<?php

use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OTPController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ReklamSlideController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\UserPlanSubscriptionController;


// Public routes
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::post('/upload/invoice', [InvoiceController::class, 'upload']);




//----------------------------------------Admin EndPoints------------------------------------------------------
// Admin middleware
Route::middleware('Admin-middleware')->group(function () {



    Route::post('/admin-login', [AuthController::class, 'adminLogin'])->name('adminLogin');
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::post('/admin-logout', [AuthController::class, 'logout']);

        // Plan Management
        Route::get('/admin-plans', [PlanController::class, 'index']);
        Route::get('/admin-plans/{id}', [PlanController::class, 'show']);
        Route::post('/admin-plans', [PlanController::class, 'store']);
        Route::put('/admin-plans/{id}', [PlanController::class, 'update']);
        Route::delete('/admin-plans/{id}', [PlanController::class, 'destroy']);

        //posts management
        Route::get('/admin-posts', [PostController::class, 'adminIndex']);
        Route::get('/admin-posts/{id}', [PostController::class, 'show']);
        Route::post('/admin-posts', [PostController::class, 'store']);
        Route::put('/admin-posts/{id}', [PostController::class, 'update']);
        Route::delete('/admin-posts/{post}', [PostController::class, 'destroy']);
        Route::put('/admin-posts/approve/{post}', [PostController::class, 'approvePost']);

        // Store Management
        Route::get('/admin-stores', [AuthController::class, 'getAllStores']);
        Route::post('/admin-stores/{userId}/change-plan/{planId}', [UserPlanSubscriptionController::class, 'changePlanByUserId']);


        // Category Management
        Route::get('/admin-categories', [CategoryController::class, 'index']);
        Route::post('/admin-categories', [CategoryController::class, 'store']);
        Route::put('/admin-categories-name/{id}', [CategoryController::class, 'updateName']);
        Route::put('/admin-categories-img/{id}', [CategoryController::class, 'updateImg']);
        Route::delete('/admin-categories/{id}', [CategoryController::class, 'destroy']);


        // Reklam Slide Management
        Route::get('/admin-reklam-slide', [ReklamSlideController::class, 'index']);
        Route::post('/admin-reklam-slide', [ReklamSlideController::class, 'store']);
        Route::get('/admin-reklam-slide/{id}', [ReklamSlideController::class, 'show']);
        Route::put('/admin-reklam-slide/{id}', [ReklamSlideController::class, 'update']);
        Route::delete('/admin-reklam-slide/{id}', [ReklamSlideController::class, 'destroy']);

    });
});


//----------------------------------------User EndPoints------------------------------------------------------
// User middleware
Route::middleware('User-middleware')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/delete-user/{id}', [AuthController::class, 'deleteUser'])->name('deleteUser');
    Route::post('/forget-password', [PasswordResetController::class, 'sendResetLink']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('all-posts', [PostController::class, 'index']);
    Route::get('/all-users', [AuthController::class, 'getAllStores']);
    Route::get('/reklam-slide', [ReklamSlideController::class, 'index']);

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', [AuthController::class, 'logout']);

        // Profile routes
        Route::get('profiles/user/{userId}', [ProfileController::class, 'getProfileByUserId']);
        Route::put('profiles/user/{userId}', [ProfileController::class, 'updateProfileByUserId']);
        Route::post('profiles/user/{userId}/upload-image', [ProfileController::class, 'uploadProfileImg']);

        // Post routes
        Route::post('posts', [PostController::class, 'store']);
        Route::get('posts', [PostController::class, 'userPosts']);
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');


    });
});







