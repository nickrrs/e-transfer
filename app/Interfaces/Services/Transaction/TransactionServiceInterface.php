<?php

namespace App\Interfaces\Services\Transaction;

use App\Models\Wallet;

interface TransactionServiceInterface
{
    public function handleTransaction(array $data);
    public function entityIsAuthorized($payer_wallet__id): bool;
    public function canTransfer(Wallet $wallet, $value): bool;
    public function getOwnerWallet($wallet_id);
    public function transaction(array $data);
}
