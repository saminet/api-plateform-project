<?php

namespace App\Controller;

use App\Handler\ChangePasswordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangePasswordController extends AbstractController
{
    public function __construct(public ChangePasswordHandler $changePasswordHandler)
    {

    }

    public function __invoke($data)
    {
    return $this->changePasswordHandler->updatePassword($data);
    }
}
