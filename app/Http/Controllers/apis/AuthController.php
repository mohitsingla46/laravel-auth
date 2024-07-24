<?php

namespace App\Http\Controllers\apis;

use Illuminate\Auth\Events\Verified;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInUserRequest;
use App\Http\Requests\SignUpUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function signup(SignUpUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = $this->userService->SignUpUser($validatedData);

        if ($user) {
            event(new Registered($user));

            return response()->json([
                'message' => 'User signed up successfully',
                'user'    => $user
            ], 201);
        }

        return response()->json([
            'message' => 'User signed up failed. Email exists.'
        ], 422);
    }

    public function signin(SignInUserRequest $request)
    {
        $validatedData = $request->validated();

        $userAdded = $this->userService->SignInUser($validatedData);

        if ($userAdded) {
            $user = $this->userService->getUserById(auth()->id());

            $token = $user->createToken('Laravel Auth')->accessToken;

            return response()->json([
                'message' => 'User signed in successfully',
                'token'   => $token
            ], 201);
        }

        return response()->json([
            'message' => 'Incorrect email or password.'
        ], 422);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Account verification email has been resent successfully.'
        ], 201);
    }

    public function verifyAccount($id)
    {
        $user = User::find($id);

        if ($user->hasVerifiedEmail()) {
            return "Your account is already verified!";
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return "Your account is now verified!";
    }

    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            'user' => $user
        ]);
    }
}
