<?php

namespace App\Interfaces\Repositories\Wallet;

use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create($entity): Wallet;
    public function search($walletId): Wallet;
    public function deposit($walletId, $value): Wallet;
    public function withdraw($walletId, $value): Wallet;
    public function delete($entity): Wallet;
}
