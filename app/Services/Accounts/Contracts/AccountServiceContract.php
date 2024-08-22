<?php

namespace App\Services\Accounts\Contracts;

interface AccountServiceContract
{
    public function create(array $data);
    public function get(array $data);
}
