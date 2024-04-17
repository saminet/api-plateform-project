<?php

namespace App\Handler;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordHandler
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {

    }

    public function updatePassword($data)
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $data,
            $data->getPassword()
        );
        $data->setPassword($hashedPassword);
        return $data;
    }

}