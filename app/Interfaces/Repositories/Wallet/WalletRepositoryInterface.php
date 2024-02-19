<?php

namespace App\Interfaces\Repositories\Wallet;

interface WalletRepositoryInterface
{
    public function create($entity);
    public function delete($entity);
}
