<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'owner_id',
        'balance'
    ];

    public function owner()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
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
