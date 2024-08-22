<?php

namespace Tests\Unit;

use App\Enums\PaymentType;
use App\Exceptions\AccountNotFoundException;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Conta;
use App\Repositories\Conta\ContaRepositoryContract;
use App\Repositories\Transacao\TransacaoRepositoryContract;
use App\Services\Transactions\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * @var int
     */
    private const TEST_ACCOUNT_NUMBER = 12345;

    protected $contaRepository;
    protected $transactionRepository;
    protected $transactionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contaRepository = app()->make(ContaRepositoryContract::class);
        $this->transactionRepository = app()->make(TransacaoRepositoryContract::class);

        $this->transactionService = new TransactionService(
            $this->contaRepository,
            $this->transactionRepository
        );
    }

    private function calculateTaxReflectedMethod($value, $paymentMethod): float
    {
        $calculateTaxReflect = $this::getMethod(
            $this->transactionService::class,
            'calculateTax'
        );

        return $calculateTaxReflect->invokeArgs($this->transactionService, [
            $value,
            $paymentMethod
        ]);

    }

    public function testLauchingNoAccountException()
    {
        $data = [
            'valor' => $this->faker->randomFloat(2, 10, 100),
            'forma_pagamento' => PaymentType::CARTAO_DEBITO->value,
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
        ];

        $this->expectException(AccountNotFoundException::class);
        $this->expectExceptionMessage('Conta não encontrada');

        $this->transactionService->handleTransaction($data);
    }

    public function testLauchingAccountWithNoBalanceException()
    {
        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => 180.37,
        ])->create();

        $data = [
            'valor' => 180,
            'forma_pagamento' => PaymentType::CARTAO_DEBITO->value,
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
        ];

        $this->expectException(InsufficientBalanceException::class);
        $this->expectExceptionMessage('Saldo insuficiente');

        $this->transactionService->handleTransaction($data);
    }

    public function testSuccessInTransaction()
    {
        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => 180.37,
        ])->create();

        $data = [
            'valor' => 150,
            'forma_pagamento' => PaymentType::CARTAO_DEBITO->value,
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
        ];

        $calculateNewBalanceValue = $this::getMethod(
            $this->transactionService::class,
            'calculateNewBalanceValue'
        );

        $newAccountBalance = $calculateNewBalanceValue->invokeArgs($this->transactionService, [
            150,
            PaymentType::from(PaymentType::CARTAO_DEBITO->value)->calculateTax(150),
            180.37,
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('account_balance_' . $data['numero_conta']);

        Cache::shouldReceive('store->put')
            ->once()
            ->with('account_balance_' . $data['numero_conta'], $newAccountBalance, 60);

        $this->transactionService->handleTransaction($data);

        $this->assertDatabaseHas('transacoes', [
            'forma_pagamento' => PaymentType::CARTAO_DEBITO->label(),
            'valor' => 150,
            'saldo_apos_transacao' => $newAccountBalance,
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
        ]);
    }

    public function testDebitTransactionTax()
    {
        $value = 100;
        $paymentMethod = PaymentType::CARTAO_DEBITO->value;;
        $expectedTax = 3; // 3% de 100

        $tax = $this->calculateTaxReflectedMethod($value, $paymentMethod);

        $this->assertEquals($expectedTax, $tax);
    }

    public function testCreditTransactionTax()
    {
        $value = 100;
        $paymentMethod = PaymentType::CARTAO_CREDITO->value;;
        $expectedTax = 5; // 5% de 100

        $tax = $this->calculateTaxReflectedMethod($value, $paymentMethod);

        $this->assertEquals($expectedTax, $tax);
    }

    public function testPixTransactionTax()
    {
        $value = 100;
        $paymentMethod = PaymentType::PIX->value;
        $expectedTax = 0;

        $tax = $this->calculateTaxReflectedMethod($value, $paymentMethod);

        $this->assertEquals($expectedTax, $tax);
    }
}
