<?php

namespace App\Observers;

use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class UserObserver
{

    public function __construct(private WalletService $walletService){

    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try{
            $this->walletService->newWallet($user);
        } catch(QueryException $queryException) {
            Log::critical("[User was created without a wallet. Error while trying to create user's wallet]", [
                'message' => $queryException->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
