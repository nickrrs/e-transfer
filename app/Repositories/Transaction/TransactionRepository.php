<?php

namespace App\Repositories\Transaction;

use App\Interfaces\Repositories\Transaction\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(private Transaction $transaction)
    {
    }
    public function create($payload): Transaction
    {
        return $this->transaction->create($payload);
    }
}
