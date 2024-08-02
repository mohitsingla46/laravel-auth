<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $userModel;

    /**
     * @param \App\Models\User $userModel
     *
     * @return void
     */
    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * @param string $email
     *
     * @return boolean
     */
    public function isEmailUnique($email)
    {
        return $this->userModel->where('email', $email)->doesntExist();
    }

    /**
     * @param array $userData
     *
     * @return \App\Models\User
     */
    public function createUser(array $userData)
    {
        return $this->userModel->create($userData);
    }

    /**
     * @param mixed $id
     *
     * @return \App\Models\User|null
     */
    public function getUserById($id)
    {
        return $this->userModel->find($id);
    }
}
