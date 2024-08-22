<?php

namespace App\Services\Transactions;

use App\Services\Transactions\Contracts\TransactionServiceContract;

class TransactionService implements TransactionServiceContract
{
    public function handleTransaction(array $data): void
    {
        dd($data);
    }
}
