<?php


namespace Aigletter\LaravelMenu\Contracts;


interface MenuRepositoryInterface
{
    public function findByName(string $name);
}