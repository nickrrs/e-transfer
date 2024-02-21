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

    public function deleted(User $user): void
    {
        try{
            $this->walletService->deleteWallet($user);
        } catch(QueryException $queryException) {
            Log::critical("[Error while trying to delete deleted user's wallet]", [
                'message' => $queryException->getMessage()
            ]);
        }
    }
}
