<?php

use App\Events\SendUserNotification;
use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepository;
use App\Services\TransactionAuthenticator\TransactionAuthenticatorService;
use App\Services\Wallet\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;
    private WalletService $walletService;
    private TransactionAuthenticatorService $mock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = new WalletService(new WalletRepository(new Wallet()));
        $this->mock = Mockery::mock(TransactionAuthenticatorService::class);
    }

    public function testUserShouldHaveEnoughMoneyToMakeATransaction(): void
    {
        $userPayer = User::factory()->create();
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(422);
    }

    public function testSellersShouldNotBeAbleToMakeATransaction(): void
    {
        $userPayer = Seller::factory()->create();
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(401);
    }

    public function testUserShouldNotMakeATransactionToHimself(): void
    {
        $user = Seller::factory()->create();

        $payload = [
            'payer_wallet_id' => $user->wallet->id,
            'payee_wallet_id' => $user->wallet->id,
            'amount' => 33
        ];

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(403);
    }

    public function testPayeerMustHaveAWalletToMakeATransaction(): void
    {
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => 'payer',
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(404);
    }

    public function testPayeeMustHaveAWalletToMakeATransaction(): void
    {
        $userPayer = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => 'payee',
            'amount' => 33
        ];

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(404);
    }

    public function testUserCanTransferMoney(): void
    {
        $userPayer = User::factory()->create();
        $this->walletService->deposit($userPayer->wallet->id, 66);
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        Event::fake();

        $request = $this->post(route('transaction.transfer'), $payload);

        $request->assertStatus(200);

        Event::assertDispatched(SendUserNotification::class);

        $this->assertDatabaseHas('wallets', [
            'id' => $userPayer->wallet->id,
            'balance' => 33.00
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $userPayee->wallet->id,
            'balance' => 33.00
        ]);
    }

    public function testUnauthorizedTransactionsCanNotBeMade(): void
    {
        $userPayer = User::factory()->create();
        $this->walletService->deposit($userPayer->wallet->id, 66);
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        $this->mock->shouldReceive('authorizeTransaction')->once()->andReturn(false);

        $this->instance(TransactionAuthenticatorService::class, $this->mock);

        $response = $this->post(route('transaction.transfer'), $payload);

        $response->assertStatus(401);
    }
}
