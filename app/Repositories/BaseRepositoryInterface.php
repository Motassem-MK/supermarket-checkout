<?php

namespace App\Repositories;

interface BaseRepositoryInterface
{
    public function beginTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}
