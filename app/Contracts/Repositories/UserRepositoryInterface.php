<?php

namespace App\Contracts\Repositories;

interface UserRepositoryInterface
{
    /**
     * @return string
     */
    public function model(): string;
}