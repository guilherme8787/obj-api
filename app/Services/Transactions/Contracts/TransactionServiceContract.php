<?php

namespace App\Services\Transactions\Contracts;

interface TransactionServiceContract
{
    public function handleTransaction(array $data);
}
