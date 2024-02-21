<?php

namespace App\Http\DTO\Transaction;

class TransactionOutputDTO
{
    public $message;
    public $amount;
    public $updated_at;
    public $created_at;

    public function __construct($data)
    {
        $this->amount = $data['amount'];
        $this->updated_at = $data['updated_at'];
        $this->created_at = $data['created_at'];
    }

    public function response()
    {
        return [
            'message' => 'Your transaction was concluded, we have sent an email to the payee.',
            'data' => [
                'amount' => $this->amount,
                'updated_at' => $this->updated_at,
                'created_at' => $this->created_at,
            ],
        ];
    }
}
