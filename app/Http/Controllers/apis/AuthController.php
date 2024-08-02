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

    /**
     * @param \App\Services\UserService $userService
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param \App\Http\Requests\SignUpUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(SignUpUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = $this->userService->SignUpUser($validatedData);

        if ($user) {
            event(new Registered($user));

            return response()->json([
                'message' => __('messages.user_signed_up_success'),
                'user'    => $user
            ], 201);
        }

        return response()->json([
            'message' => __('messages.user_signed_up_failed')
        ], 422);
    }

    /**
     * @param \App\Http\Requests\SignInUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signin(SignInUserRequest $request)
    {
        $validatedData = $request->validated();

        $userAdded = $this->userService->SignInUser($validatedData);

        if ($userAdded) {
            $user = $this->userService->getUserById(auth()->id());

            $token = $user->createToken('Laravel Auth')->accessToken;

            return response()->json([
                'message' => __('messages.user_signed_in_success'),
                'token'   => $token
            ], 201);
        }

        return response()->json([
            'message' => __('messages.incorrect_email_or_password')
        ], 422);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => __('messages.account_verification_email_resent')
        ], 201);
    }

    /**
     * @param integer $id
     *
     * @return string
     */
    public function verifyAccount($id)
    {
        $user = User::find($id);

        if ($user->hasVerifiedEmail()) {
            return __('messages.your_account_is_already_verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return __('messages.your_account_is_now_verified');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth()->user();

        return response()->json([
            'user' => $user
        ]);
    }
}
