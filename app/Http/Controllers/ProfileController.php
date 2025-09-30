<?php

namespace App\Http\Controllers;

use App\Services\PasswordService;
use App\Models\User;
use App\Traits\AppTrait;
use App\Http\Resources\UserResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use AppTrait;
    protected PasswordService $passwordService;

    public function index(){
        return $this->apiResponse(success: true, message: __('app.USER_INFO'), data: 
            new UserResource(auth()->user()->load('profile'))
        );
    }

    public function updatePassword(Request $request)
    {
        return $this->passwordService->updatePassword($request);
    }

    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password does not match'], 403);
        }

        $user->profile->primary_email = $request->email;
        $user->profile->save();

        $verificationUrl = URL::temporarySignedRoute(
            'new.email.verify', Carbon::now()->addMinutes(60), ['user' => $user->id]
        );

        Mail::raw("Click to verify your new email: $verificationUrl", function ($m) use ($request) {
            $m->to($request->email)->subject('Verify your new email');
        });

        return response()->json(['message' => 'Verification email sent to the new address.']);
    }

    public function verifyNewEmail(Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired verification link'], 403);
        }

        $user = User::findOrFail($request->user);

        if (!$user->profile || !$user->profile->primary_email) {
            return response()->json(['message' => 'No pending email change found'], 404);
        }

        $user->email = $user->profile->primary_email;
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Email verified and updated successfully.']);
    }

}
