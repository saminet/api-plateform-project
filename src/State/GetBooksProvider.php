<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class GetBooksProvider implements ProviderInterface
{
    public function __construct(private ProviderInterface $provider)
    {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        die('ici !!!');
        $book = $this->provider->provide($operation, $uriVariables, $context);
        dump($book->getQuery()->getResult()[0]);die;
        return $book;
    }
}
