<?php

namespace App\Services\Accounts\Contracts;

interface AccountServiceContract
{
    public function create(array $data): void;
    public function get(array $data): array;
}
