<?php

namespace App\Services\Wallet;

use App\Interfaces\Services\Wallet\WalletServiceInterface;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepository;

class WalletService implements WalletServiceInterface
{
    public function __construct(private WalletRepository $walletRepository)
    {
    }

    public function newWallet($entity): Wallet
    {
        return $this->walletRepository->create($entity);
    }

    public function findWallet($walletId): Wallet
    {
        return $this->walletRepository->search($walletId);
    }

    public function deposit($walletId, $value): Wallet
    {
        return $this->walletRepository->deposit($walletId, $value);
    }

    public function withdraw($walletId, $value): Wallet
    {
        return $this->walletRepository->withdraw($walletId, $value);
    }
    public function deleteWallet($entity): Wallet
    {
        return $this->walletRepository->delete($entity);
    }
}
