<?php

namespace App\Interfaces\Services\Transaction;

use App\Models\Transaction;
use App\Models\Wallet;

interface TransactionServiceInterface
{
    public function handleTransaction(array $data): Transaction;
    public function entityIsAuthorized(string $payerWalletId): bool;
    public function canTransfer(Wallet $wallet, float $value): bool;
    public function getWallet(string $walletId): Wallet | bool;
    public function transaction(array $data): Transaction;
}
