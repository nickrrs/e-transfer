<?php

namespace App\Services\Transaction;

use App\Events\SendUserNotification;
use App\Exceptions\TransactionDeniedException;
use App\Exceptions\TransactionUnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Interfaces\Services\Transaction\TransactionServiceInterface;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\Transaction\TransactionRepository;
use App\Services\TransactionAuthenticator\TransactionAuthenticatorService;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TransactionService implements TransactionServiceInterface
{
    public function __construct(
        private WalletService $walletService,
        private TransactionRepository $transactionRepository,
        private TransactionAuthenticatorService $authenticatorService,
    ) {
    }

    public function handleTransaction(array $data): Transaction
    {
        if (!$this->getWallet($data['payer_wallet_id'])) {
            throw new ModelNotFoundException('The payer wallet was nout found on the system.', 404);
        }

        if (!$this->getWallet($data['payee_wallet_id'])) {
            throw new ModelNotFoundException('The payee wallet was not found on the system', 404);
        }

        if ($data['payer_wallet_id'] == $data['payee_wallet_id']) {
            throw new TransactionDeniedException('An user cant make a transaction to the same wallet.', 403);
        }

        if (!$this->entityIsAuthorized($data['payer_wallet_id'])) {
            throw new TransactionDeniedException('A seller is not authorized to make a transaction.', 401);
        }

        $payerWallet = $this->getWallet($data['payer_wallet_id']);

        if (!$this->canTransfer($payerWallet, $data['amount'])) {
            throw new TransactionDeniedException('The payer dont have enough money to make this transacion.', 422);
        }

        if (!$this->authenticatorService->authorizeTransaction()) {
            throw new TransactionUnauthorizedException('This transaction was not authorized, please try again later.', 401);
        }

        return $this->transaction($data);
    }

    public function entityIsAuthorized($payerWalletId): bool
    {
        if ($this->getWallet($payerWalletId)->isOwnerAnUser()) {
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

    public function getWallet($walletId): Wallet | bool
    {
        try {
            return $this->walletService->findWallet($walletId);
        } catch (\Exception $e) {
            Log::error("[A wallet was not found on the database during transaction operation]", [
                'message' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function transaction(array $data): Transaction
    {
        $payload = [
            'id' => Uuid::uuid4()->toString(),
            'payer_wallet_id' => $data['payer_wallet_id'],
            'payee_wallet_id' => $data['payee_wallet_id'],
            'amount' => $data['amount']
        ];

        $payeeInfo = $this->getWallet($payload['payee_wallet_id'])->owner;

        return DB::transaction(function () use ($payload, $payeeInfo) {
            $transaction = $this->transactionRepository->create($payload);
            $this->walletService->withdraw($payload['payer_wallet_id'], $payload['amount']);
            $this->walletService->deposit($payload['payee_wallet_id'], $payload['amount']);

            SendUserNotification::dispatch($payeeInfo, $transaction);

            return $transaction;
        });
    }
}
