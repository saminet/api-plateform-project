<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private ProcessorInterface $processor
    ){}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $data,
            $data->getPassword()
        );
        $data->setPassword($hashedPassword);

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
