<?php

namespace App\Interfaces\Services\TransactionAuthenticator;

interface TransactionAuthenticatorServiceInterface
{
    public function authorizeTransaction(): bool;
}
