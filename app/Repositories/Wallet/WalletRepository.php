<?php

namespace App\Repositories\Wallet;

use App\Interfaces\Repositories\Wallet\WalletRepositoryInterface;
use App\Models\Wallet;
use Ramsey\Uuid\Uuid;

class WalletRepository implements WalletRepositoryInterface
{
    public function __construct(private Wallet $wallet)
    {
    }

    public function create($entity): Wallet
    {
        $wallet = new Wallet();

        $wallet->id = Uuid::uuid4()->toString();
        $wallet->owner_id = $entity->id;
        $wallet->owner()->associate($entity);
        $wallet->balance = 0;

        $wallet->save();

        return $wallet;
    }

    public function search($walletId): Wallet
    {
        $wallet = $this->wallet->findOrFail($walletId);
        return $wallet;
    }

    public function deposit($walletId, $value): Wallet
    {
        $wallet = $this->search($walletId);
        $wallet->update([
            'balance' => $wallet->balance + $value
        ]);

        return $wallet;
    }

    public function withdraw($walletId, $value): Wallet
    {
        $wallet = $this->search($walletId);
        $wallet->update([
            'balance' => $wallet->balance - $value
        ]);

        return $wallet;
    }
    public function delete($entity): Wallet
    {
        $wallet = $this->wallet->where('owner_id', $entity->id)->firstOrFail();
        $wallet->delete();

        return $wallet;
    }
}
