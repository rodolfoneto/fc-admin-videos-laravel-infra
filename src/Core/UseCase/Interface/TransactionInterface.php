<?php

namespace Core\UseCase\Interface;

interface TransactionInterface
{
    public function commit();
    public function rollback();
}
