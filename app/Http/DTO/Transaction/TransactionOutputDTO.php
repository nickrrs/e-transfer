<?php

namespace App\Http\DTO\Transaction;

class TransactionOutputDTO
{
    public $message;
    public $amount;
    public $updatedAt;
    public $createdAt;

    public function __construct($data)
    {
        $this->amount = $data['amount'];
        $this->updatedAt = $data['updated_at'];
        $this->createdAt = $data['created_at'];
    }

    public function response(): array
    {
        return [
            'message' => 'Your transaction was concluded, we have sent an email to the payee.',
            'data' => [
                'amount' => $this->amount,
                'updated_at' => $this->updatedAt,
                'created_at' => $this->createdAt,
            ],
        ];
    }
}
