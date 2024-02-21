<?php

namespace App\Interfaces\Services\Wallet;

use App\Models\Wallet;

interface WalletServiceInterface {
    public function newWallet($entity): Wallet;
    public function findWallet($walletId): Wallet;
    public function deposit($walletId, $value): Wallet;
    public function withdraw($walletId, $value): Wallet;
    public function deleteWallet($entity): Wallet;
}