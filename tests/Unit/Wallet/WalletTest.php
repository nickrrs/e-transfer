<?php

use App\Models\Seller;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatedUserShouldHaveAWallet(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('wallets', [
            'owner_id' => $user->id,
        ]);
    }

    public function testCreatedSellerShouldHaveAWallet(): void
    {
        $seller = Seller::factory()->create();

        $this->assertDatabaseHas('wallets', [
            'owner_id' => $seller->id,
        ]);
    }

    public function testADeletedUserOrSellerShouldHaveHisWalletDeleted(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->assertDatabaseMissing('wallets', [
            'owner_id' => $user->id,
        ]);
    }

    public function testANewWalletShouldHaveAnAmountOfZero(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('wallets', [
            'owner_id' => $user->id,
            'balance' => 0
        ]);
    }
}
