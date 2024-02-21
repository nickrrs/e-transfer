<?php

namespace App\Observers;

use App\Models\Seller;
use App\Services\Wallet\WalletService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SellerObserver
{
    public function __construct(private WalletService $walletService)
    {
    }

    /**
     * Handle the Seller "created" event.
     */
    public function created(Seller $seller): void
    {
        try {
            $this->walletService->newWallet($seller);
        } catch (QueryException $queryException) {
            Log::critical("[Seller was created without a wallet. Error while trying to create seller's wallet]", [
                'message' => $queryException->getMessage()
            ]);
        }
    }

    /**
     * Handle the Seller "deleted" event.
     */
    public function deleted(Seller $seller): void
    {
        try{
            $this->walletService->deleteWallet($seller);
        } catch(QueryException $queryException) {
            Log::critical("[Error while trying to delete deleted seller's wallet]", [
                'message' => $queryException->getMessage()
            ]);
        }
    }
}
