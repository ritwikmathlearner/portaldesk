<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Discussion extends Pivot
{
    protected $fillable = ['user_id', 'task_id', 'message', 'type'];
}
