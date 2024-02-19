<?php

namespace App\Services\Transaction;

use App\Exceptions\TransactionDeniedException;
use App\Exceptions\TransactionUnauthorizedException;
use App\Services\ThirdParty\ThirdPartyService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Services\Transaction\TransactionServiceInterface;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TransactionService implements TransactionServiceInterface
{

    public function __construct(private WalletRepository $walletRepository, private ThirdPartyService $thirdPartyService)
    {
    }

    public function handleTransaction(array $data)
    {

        if (!$this->getOwnerWallet($data['payer_wallet_id'])) {
            throw new ModelNotFoundException('The payer was nout found on the system.', 404);
        }

        if ($data['payer_wallet_id'] == $data['payee_wallet_id']) {
            throw new TransactionDeniedException('An user cant make a transaction to the same wallet.');
        }

        if (!$this->entityIsAuthorized($data['payer_wallet_id'])) {
            throw new TransactionDeniedException('A seller is not authorized to make a transaction.');
        }

        $payerWallet = $this->getOwnerWallet($data['payer_wallet_id']);

        if (!$this->canTransfer($payerWallet, $data['amount'])) {
            throw new TransactionDeniedException('The payer dont have enough money to make this transacion.');
        }

        if(!$this->thirdPartyService->authorizeTransaction()){
            throw new TransactionUnauthorizedException('This transaction was not authorized, please try again later.', 403);
        }

        return $this->transaction($data);
    }

    public function entityIsAuthorized($payer_wallet_id): bool
    {
        if ($this->getOwnerWallet($payer_wallet_id)->isOwnerAnUser()) {
            return true;
        }

        return false;
    }

    public function canTransfer(Wallet $wallet, $value): bool
    {
        if ($wallet->balance >= $value) {
            return true;
        }

        return false;
    }

    public function getOwnerWallet($payer_wallet_id)
    {
        try {
            return $this->walletRepository->indexByOwner($payer_wallet_id);
        } catch (\Exception $e) {
            Log::critical("[Error while trying to retrieve wallets owner information of the transaction operation.]", [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    public function transaction(array $data)
    {
        $payload = [
            'id' => Uuid::uuid4()->toString(),
            'payer_wallet_id' => $data['payer_wallet_id'],
            'payee_wallet_id' => $data['payee_wallet_id'],
            'amount' => $data['amount']
        ];

        return DB::transaction(function () use ($payload) {
            $transaction = Transaction::create($payload);
            $this->walletRepository->withdraw($payload['payer_wallet_id'], $payload['amount']);
            $this->walletRepository->deposit($payload['payee_wallet_id'], $payload['amount']);

            return $transaction;
        });
    }
}
