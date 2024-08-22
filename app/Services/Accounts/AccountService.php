<?php

namespace App\Services\Accounts;

use App\Exceptions\AccountNotFoundException;
use App\Http\Resources\AccountResource;
use App\Models\Conta;
use App\Repositories\Conta\ContaRepositoryContract;
use App\Services\Accounts\Contracts\AccountServiceContract;
use Illuminate\Support\Facades\Cache;

class AccountService implements AccountServiceContract
{
    /**
     * @var int
     */
    private const CACHE_TTL = 60;

    public function __construct(
        private ContaRepositoryContract $contaRepository
    ) {}

    private function getAccountBalanceFromCache(string $accountNumber): ?float
    {
        return Cache::store('redis')->get('account_balance_' . $accountNumber);
    }

    public function create(array $data): void
    {
        $account = $this->contaRepository->create($data);

        Cache::store('redis')->put(
            'account_balance_' . $account->numero_conta,
            $account->saldo,
            self::CACHE_TTL
        );
    }

    /**
     * @throws AccountNotFoundException
     */
    public function get(array $data): AccountResource
    {
        $accountNumber = data_get($data, 'numero_conta');

        if (!$accountNumber) {
            throw new AccountNotFoundException('Número da conta não informado');
        }

        $balance = $this->getAccountBalanceFromCache($accountNumber);

        if ($balance) {
            $account = [
                'numero_conta' => $accountNumber,
                'saldo' => $balance,
            ];

            return new AccountResource((object) $account);
        }

        $accountStatus = $this->contaRepository->findByAccountNumber($accountNumber);

        if (!$accountStatus) {
            throw new AccountNotFoundException('Conta não encontrada');
        }

        Cache::store('redis')->put(
            'account_balance_' . $accountStatus->numero_conta,
            $accountStatus->saldo,
            self::CACHE_TTL
        );

        return new AccountResource($accountStatus);
    }
}
