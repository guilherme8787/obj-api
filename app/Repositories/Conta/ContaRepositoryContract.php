<?php

namespace App\Repositories\Conta;

use App\Models\Conta;

interface ContaRepositoryContract
{
    public function findByAccountNumber(int $accountNumber): ?Conta;
    public function updateBalance(int $accountNumber, float $value): bool;
}
