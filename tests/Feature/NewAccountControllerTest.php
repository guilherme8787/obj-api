<?php

namespace Tests\Feature;

use App\Models\Conta;
use App\Services\Accounts\AccountService;
use App\Services\Accounts\Contracts\AccountServiceContract;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class NewAccountControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var int
     */
    private const TEST_ACCOUNT_NUMBER = 12345;

    /**
     * @var int
     */
    private const TEST_ACCOUNT_WRONG_NUMBER = 1223;

    /**
     * @var float
     */
    private const TEST_BALANCE_NUMBER = 10.0;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSuccessfullCreateAccountStatus()
    {
        $data = [
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => self::TEST_BALANCE_NUMBER
        ];

        $this->instance(
            AccountServiceContract::class,
            Mockery::mock(AccountServiceContract::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('create')
                ->once()
                ->with($data)
                ->andReturn(new Conta([
                    'numero_conta' => self::TEST_ACCOUNT_NUMBER,
                    'saldo' => self::TEST_BALANCE_NUMBER
                ]));
            })
        );

        $response = $this->postJson('/conta', $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson($data);
    }

    public function testErroWithAccountNumberExists()
    {
        $data = [
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => self::TEST_BALANCE_NUMBER
        ];

        Conta::factory()->state($data)->create();

        $response = $this->postJson('/conta', $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'Esse número de conta já existe.',
            'errors' => [
                'numero_conta' => [
                    'Esse número de conta já existe.',
                ],
            ],
        ]);
    }

    public function testErroWithSomeException()
    {
        $data = [
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => self::TEST_BALANCE_NUMBER
        ];

        $this->instance(
            AccountServiceContract::class,
            Mockery::mock(AccountServiceContract::class, function (MockInterface $mock) use ($data) {
                $mock->shouldReceive('create')
                ->once()
                ->with($data)
                ->andThrow(new Exception('Erro ao criar conta'));
            })
        );

        Log::shouldReceive('error')->times(1);

        $response = $this->postJson('/conta', $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'message' => 'Erro ao criar conta',
        ]);
    }
}
