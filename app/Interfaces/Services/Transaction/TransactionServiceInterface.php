<?php

namespace App\Interfaces\Services\Transaction;

use App\Models\Transaction;
use App\Models\Wallet;

interface TransactionServiceInterface
{
    public function handleTransaction(array $data): Transaction;
    public function entityIsAuthorized($payerWalletId): bool;
    public function canTransfer(Wallet $wallet, $value): bool;
    public function getWallet($walletId): Wallet | bool;
    public function transaction(array $data): Transaction;
}
