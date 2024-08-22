<?php

namespace App\Services\Accounts;

use App\Exceptions\AccountNotFoundException;
use App\Http\Resources\AccountResource;
use App\Repositories\Conta\ContaRepositoryContract;
use App\Services\Accounts\Contracts\AccountServiceContract;

class AccountService implements AccountServiceContract
{
    public function __construct(
        private ContaRepositoryContract $contaRepository
    ) {}

    public function create(array $data): void
    {
        $this->contaRepository->create($data);
    }

    /**
     * @throws AccountNotFoundException
     */
    public function get(array $data): AccountResource
    {
        $accountNumber = data_get($data, 'numero_conta');

        $accountStatus = $this->contaRepository->findByAccountNumber($accountNumber);

        if (! $accountStatus) {
            throw new AccountNotFoundException('Conta não encontrada');
        }

        return new AccountResource($accountStatus);
    }
}
