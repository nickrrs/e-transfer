<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'payee_wallet_id',
        'payer_wallet_id',
        'amount'
    ];

    public function walletPayer(): BelongsTo
    {
        return $this->belongsTo(Wallet::class,  'payer_wallet_id');
    }

    public function walletPayee(): BelongsTo
    {
        return $this->belongsTo(Wallet::class,  'payee_wallet_id');
    }
}
