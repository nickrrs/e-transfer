<?php

namespace App\Repositories\Wallet;

use App\Interfaces\Repositories\Wallet\WalletRepositoryInterface;
use App\Models\Wallet;
use Ramsey\Uuid\Uuid;

class WalletRepository implements WalletRepositoryInterface
{
    public function create($entity)
    {
        $wallet = new Wallet();

        $wallet->id = Uuid::uuid4()->toString();
        $wallet->owner()->associate($entity);
        $wallet->balance = 0;

        $wallet->save();

        return $wallet;
    }
}
