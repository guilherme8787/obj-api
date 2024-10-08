<?php

namespace App\Repositories\Conta;

use App\Models\Conta;

class ContaRepository implements ContaRepositoryContract
{
    private Conta $conta;

    public function __construct(Conta $conta)
    {
        $this->conta = $conta;
    }

    public function findByAccountNumber(int $accountNumber): ?Conta
    {
        return $this->conta->where('numero_conta', $accountNumber)->first();
    }

    public function updateBalance(int $accountNumber, float $value): bool
    {
        return $this->conta->where('numero_conta', $accountNumber)->update([
            'saldo' => $value,
        ]);
    }

    public function create(array $data): Conta
    {
        return $this->conta->create($data);
    }
}
