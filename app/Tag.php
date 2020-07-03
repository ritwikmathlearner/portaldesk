<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag_name'];

    public function tasks()
    {
        return $this->belongsToMany('App\Task')->withTimestamps();
    }
}
