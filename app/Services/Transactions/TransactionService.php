<?php

namespace App\Services\Transactions;

use App\Enums\PaymentType;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientBalanceException;
use App\Repositories\Conta\ContaRepositoryContract;
use App\Repositories\Transacao\TransacaoRepositoryContract;
use App\Services\Transactions\Contracts\TransactionServiceContract;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransactionService implements TransactionServiceContract
{
    public function __construct(
        private ContaRepositoryContract $contaRepository,
        private TransacaoRepositoryContract $transactionRepository
    ) {}

    private function getPaymentMethod(string $paymentMethod): string
    {
        $paymentType = PaymentType::from($paymentMethod);

        return $paymentType->label();
    }

    private function calculateTax(float $value, string $paymentMethod): float
    {
        $paymentType = PaymentType::from($paymentMethod);

        return $paymentType->calculateTax($value);
    }

    private function calculateNewBalanceValue(
        float $value,
        float $tax,
        float $accountBalance
    ): float{
        return $accountBalance - ($value + $tax);
    }

    private function getAccountBalance(int $accountNumber): float
    {
        $accountStatus = $this->contaRepository->findByAccountNumber($accountNumber);

        if (!$accountStatus) {
            throw new AccountNotFoundException('Conta não encontrada');
        }

        return $accountStatus?->saldo ? $accountStatus->saldo : 0;
    }

    /**
     * @throws  Exception
     * @throws  InsufficientBalanceException
     */
    public function handleTransaction(array $data): ?array
    {
        $value = data_get($data, 'valor');
        $paymentMethod = data_get($data, 'forma_pagamento');
        $accountNumber = data_get($data, 'numero_conta');

        $tax = $this->calculateTax($value, $paymentMethod);
        $accountBalance = $this->getAccountBalance($accountNumber);
        $newAccountBalance = $this->calculateNewBalanceValue($value, $tax, $accountBalance);

        if ($newAccountBalance < 0) {
            throw new InsufficientBalanceException('Saldo insuficiente');
        }

        DB::beginTransaction();
        try {
            $this->transactionRepository->create([
                'forma_pagamento' => $this->getPaymentMethod($paymentMethod),
                'valor' => $value,
                'saldo_apos_transacao' => $newAccountBalance,
                'valor_final' => $accountBalance,
                'numero_conta' => $accountNumber
            ]);

            $this->contaRepository->updateBalance($accountNumber, $newAccountBalance);

            Cache::forget('account_balance_' . $accountNumber);
            Cache::store('redis')->put('account_balance_' . $accountNumber, $newAccountBalance, 60);

            DB::commit();

            return [
                'numero_conta' => $accountNumber,
                'saldo' => number_format($newAccountBalance, 2)
            ];

        } catch (Exception|Throwable $exception) {
            DB::rollBack();

            throw new Exception($exception->getMessage());
        }
    }
}
