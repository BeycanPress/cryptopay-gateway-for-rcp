<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\RCP\Models;

use BeycanPress\CryptoPay\Models\AbstractTransaction;

class TransactionsPro extends AbstractTransaction
{
    public string $addon = 'rcp';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct('rcp_transaction');
    }
}
