<?php

namespace App\Http\DTO\Transaction;

class TransactionOutputDTO
{
    public function __construct(public $amount, public $updatedAt, public $createdAt)
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            updatedAt: $data['updated_at'],
            createdAt: $data['created_at'],
        );
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
