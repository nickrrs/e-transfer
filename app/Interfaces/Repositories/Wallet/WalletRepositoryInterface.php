<?php

namespace App\Interfaces\Repositories\Wallet;

interface WalletRepositoryInterface
{
    public function create($entity);
    public function indexByOwner($id);
    public function delete($entity);
}
