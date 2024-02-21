<?php

namespace App\Services\Wallet;

use App\Interfaces\Services\Wallet\WalletServiceInterface;
use App\Models\Seller;
use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Wallet\WalletRepository;

class WalletService implements WalletServiceInterface
{
    public function __construct(private WalletRepository $walletRepository)
    {
    }

    public function newWallet(User | Seller $entity): Wallet
    {
        return $this->walletRepository->create($entity);
    }

    public function findWallet(string $walletId): Wallet
    {
        return $this->walletRepository->search($walletId);
    }

    public function deposit(string $walletId, float $value): Wallet
    {
        return $this->walletRepository->deposit($walletId, $value);
    }

    public function withdraw(string $walletId, float $value): Wallet
    {
        return $this->walletRepository->withdraw($walletId, $value);
    }
    
    public function deleteWallet(User | Seller $entity): Wallet
    {
        return $this->walletRepository->delete($entity);
    }
}
