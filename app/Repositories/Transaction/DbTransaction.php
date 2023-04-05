<?php

namespace  App\Repositories\Transaction;

use Core\UseCase\Interface\TransactionInterface;
use Illuminate\Support\Facades\DB;

class DbTransaction implements TransactionInterface
{
    public function __construct()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollback()
    {
        DB::rollBack();
    }
}
