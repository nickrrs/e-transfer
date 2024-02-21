<?php

namespace App\Repositories\Wallet;

use App\Interfaces\Repositories\Wallet\WalletRepositoryInterface;
use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;
use Ramsey\Uuid\Uuid;

class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(private Wallet $wallet)
    {
    }

    public function create(User | Seller $entity): Wallet
    {
        $this->wallet->id = Uuid::uuid4()->toString();
        $this->wallet->owner_id = $entity->id;

        $this->wallet->owner()->associate($entity);
        
        $this->wallet->balance = 0;

        $this->wallet->save();

        return $this->wallet;
    }

    public function search(string $walletId): Wallet
    {
        return $this->wallet->findOrFail($walletId);
    }

    public function deposit(string $walletId, float $value): Wallet
    {
        $wallet = $this->search($walletId);
        
        $wallet->update([
            'balance' => $wallet->balance + $value
        ]);

        return $wallet;
    }

    public function withdraw(string $walletId, float $value): Wallet
    {
        $wallet = $this->search($walletId);

        $wallet->update([
            'balance' => $wallet->balance - $value
        ]);

        return $wallet;
    }
    public function delete(User | Seller $entity): Wallet
    {
        $wallet = $this->wallet->where('owner_id', $entity->id)->firstOrFail();
        
        $wallet->delete();

        return $wallet;
    }
}
