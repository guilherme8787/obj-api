<?php

namespace Tests\Feature;

use App\Models\Conta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ShowAccountControllerTest extends TestCase
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

    public function testSuccessfulShowAccountStatus()
    {
        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => self::TEST_BALANCE_NUMBER,
        ])->create();

        $response = $this->get('/conta?numero_conta=' . self::TEST_ACCOUNT_NUMBER);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => self::TEST_BALANCE_NUMBER,
        ]);
    }

    public function testErrorInShowAccountStatusWithWrongFields()
    {
        $fakeBalance = $this->faker->randomFloat(2, 1, 1000);

        Conta::factory()->state([
            'numero_conta' => self::TEST_ACCOUNT_NUMBER,
            'saldo' => $fakeBalance,
        ])->create();

        $wrongQueryString = 'numer21conta=' . $this->faker->randomNumber(5);

        Log::shouldReceive('info')->times(1);

        $response = $this->get('/conta?' . $wrongQueryString . self::TEST_ACCOUNT_NUMBER);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'message' => 'Número da conta não informado',
        ]);
    }

    public function testErrorInShowAccountStatusWithNotFoundAccount()
    {
        Log::shouldReceive('info')->times(1);

        $response = $this->get('/conta?numero_conta=' . self::TEST_ACCOUNT_WRONG_NUMBER);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'message' => 'Conta não encontrada',
        ]);
    }
}
