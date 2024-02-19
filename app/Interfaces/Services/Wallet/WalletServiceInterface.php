<?php

namespace App\Interfaces\Services\Wallet;

interface WalletServiceInterface {
    public function newWallet($entity);
    public function deleteWallet($entity);
}