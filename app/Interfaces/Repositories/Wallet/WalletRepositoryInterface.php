<?php

namespace App\Interfaces\Repositories\Wallet;

use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create(User | Seller $entity): Wallet;
    public function search(string $walletId): Wallet;
    public function deposit(string $walletId, float $value): Wallet;
    public function withdraw(string $walletId, float $value): Wallet;
    public function delete(User | Seller $entity): Wallet;
}
