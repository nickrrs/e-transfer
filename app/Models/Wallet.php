<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'owner_id',
        'balance'
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isOwnerAnUser(): bool
    {
        return $this->owner_type === 'App\Models\User';
    }

    public function isOwnerASeller(): bool
    {
        return $this->owner_type === 'App\Models\Seller';
    }
}
