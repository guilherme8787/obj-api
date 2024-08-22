<?php

namespace App\Services\Accounts\Contracts;

use App\Http\Resources\AccountResource;

interface AccountServiceContract
{
    public function create(array $data): void;
    public function get(array $data): AccountResource;
}
