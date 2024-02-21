<?php

namespace App\Interfaces\Services\Wallet;

use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;

interface WalletServiceInterface {
    public function newWallet(User | Seller $entity): Wallet;
    public function findWallet(string $walletId): Wallet;
    public function deposit(string $walletId, float $value): Wallet;
    public function withdraw(string $walletId, float $value): Wallet;
    public function deleteWallet(User | Seller $entity): Wallet;
}