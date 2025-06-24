<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Routing\Controller;

class VerificationController extends Controller
{
    /**
     * Handle email verification.
     *
     * @param \Illuminate\Foundation\Auth\EmailVerificationRequest $request
     * @param int $id
     * @param string $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($id, $hash)
    {

        $user = User::findOrFail($id);

        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['error' => 'Invalid verification link'], 401);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return redirect()->route('verified');
    }
}
