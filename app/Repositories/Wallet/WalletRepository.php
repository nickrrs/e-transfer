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
        $wallet->owner_id = $entity->id;
        $wallet->owner()->associate($entity);
        $wallet->balance = 0;

        $wallet->save();

        return $wallet;
    }

    public function indexByOwner($id)
    {
        $wallet = Wallet::where('owner_id', $id)->firstOrFail();
        return $wallet;
    }

    public function deposit($id, $value)
    {
        $wallet = $this->indexByOwner($id);
        $wallet->update([
            'balance' => $wallet->balance + $value
        ]);

        return $wallet;
    }

    public function withdraw($id, $value)
    {
        $wallet = $this->indexByOwner($id);
        $wallet->update([
            'balance' => $wallet->balance - $value
        ]);

        return $wallet;
    }
    public function delete($entity)
    {
        $wallet = Wallet::where('owner_id', $entity->id)->firstOrFail();
        $wallet->delete();
    }
}
