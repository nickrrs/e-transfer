<?php

namespace App\Repositories\Transaction;

use App\Interfaces\Repositories\Transaction\TransactionRepositoryInterface;
use App\Models\Transaction;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create($payload): Transaction{
        return Transaction::create($payload);
    }
}
