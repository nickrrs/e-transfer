<?php

namespace App\Services\Wallet;

use App\Interfaces\Services\Wallet\WalletServiceInterface;
use App\Repositories\Wallet\WalletRepository;

class WalletService implements WalletServiceInterface
{

    private $walletRepository;

    public function __construct(WalletRepository $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    public function newWallet($entity)
    {
        return $this->walletRepository->create($entity);
    }
}
