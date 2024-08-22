<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Conta;
use App\Services\Transactions\Contracts\TransactionServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var int
     */
    private const TEST_ACCOUNT_NUMBER = 12345;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccessfulTransaction()
    {
        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => 180.37,
        ])->create();

        $data = [
            'forma_pagamento' => 'D',
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'valor' => 9
        ];

        $this->instance(
            TransactionServiceContract::class,
            Mockery::mock(TransactionServiceContract::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('handleTransaction')
                    ->once()
                    ->with($data)
                    ->andReturn([
                        'numero_conta' => self::TEST_ACCOUNT_NUMBER,
                        'saldo' => 90.70
                    ]);
            })
        );

        $response = $this->postJson('/transacao', $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => 90.70
        ]);
    }

    public function testAccountNotFoundException()
    {
        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => 180.37,
        ])->create();

        $data = [
            'forma_pagamento' => 'D',
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'valor' => 200
        ];

        Log::shouldReceive('info')->times(1);

        $this->instance(
            TransactionServiceContract::class,
            Mockery::mock(TransactionServiceContract::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('handleTransaction')
                    ->once()
                    ->with($data)
                    ->andThrow(new InsufficientBalanceException('Saldo insuficiente'));
            })
        );

        $response = $this->postJson('/transacao', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'message' => 'Saldo insuficiente'
        ]);
    }
}
