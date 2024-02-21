<?php

use App\Events\SendUserNotification;
use App\Models\Seller;
use App\Models\User;
use App\Repositories\Wallet\WalletRepository;
use App\Services\TransactionAuthenticator\TransactionAuthenticatorService;
use App\Services\Wallet\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TransactionsTest extends TestCase
{
    use RefreshDatabase;
    private $walletService;
    //stub for wallet service
    protected function setUp(): void
    {
        parent::setUp();
        // Substitua WalletRepository por um stub, se necessário
        $this->walletService = new WalletService(new WalletRepository());
    }

    public function testUserShouldHaveEnoughMoneyToMakeATransaction()
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

    public function testSellersShouldNotBeAbleToMakeATransaction()
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

    public function testUserShouldNotMakeATransactionToHimself()
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

    public function testPayeerMustHaveAWalletToMakeATransaction()
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

    public function testPayeeMustHaveAWalletToMakeATransaction()
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

    public function testUserCanTransferMoney()
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

    public function testUnauthorizedTransactionsCanNotBeMade()
    {
        $userPayer = User::factory()->create();
        $this->walletService->deposit($userPayer->wallet->id, 66);
        $userPayee = User::factory()->create();

        $payload = [
            'payer_wallet_id' => $userPayer->wallet->id,
            'payee_wallet_id' => $userPayee->wallet->id,
            'amount' => 33
        ];

        $mock = Mockery::mock(TransactionAuthenticatorService::class);
        $mock->shouldReceive('authorizeTransaction')->once()->andReturn(false);

        $this->app->instance(TransactionAuthenticatorService::class, $mock);


        $response = $this->post(route('transaction.transfer'), $payload);

        $response->assertStatus(401);
    }
}
