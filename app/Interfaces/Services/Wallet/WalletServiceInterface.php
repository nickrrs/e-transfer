<?php

namespace App\Interfaces\Services\Wallet;

interface WalletServiceInterface {
    public function newWallet($entity);
    public function findOwnerWallet($id);
    public function deleteWallet($entity);
}