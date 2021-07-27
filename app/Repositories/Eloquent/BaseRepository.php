<?php

namespace App\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    public function __construct(protected Model $model)
    {
    }
}
