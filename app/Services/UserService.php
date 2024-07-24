<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function SignUpUser(array $userData)
    {
        if ($this->userRepository->isEmailUnique($userData['email'])) {

            $userData['password'] = Hash::make($userData['password']);

            $user = $this->userRepository->createUser($userData);

            return $user;
        }

        return null;
    }

    public function SignInUser(array $userData)
    {
        if (Auth::attempt($userData)) {
            return Auth::user();
        } else {
            return null;
        }
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }
}
