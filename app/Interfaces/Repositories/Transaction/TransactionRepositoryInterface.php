<?php

namespace App\Interfaces\Repositories\Transaction;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(array $payload): Transaction;
}
