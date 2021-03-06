<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tasks()
    {
        return $this->hasMany('App\Task', 'create_by');
    }

    public function assignedTasks()
    {
        return $this->hasMany('App\Task', 'is_allocated_to');
    }

    public function invitedTasks()
    {
        return $this->belongsToMany('App\Task', 'task_invitation')->withTimestamps();
    }

    public function taskDiscussions()
    {
        return $this->belongsToMany('App\Task', 'discussions')
            ->using('App\Discussion')
            ->withPivot([
                'message',
                'type',
                'created_at'
            ]);
    }

    public function isAnOwner()
    {
        if ($this->role === 'owner') {
            return true;
        } else {
            return false;
        }
    }

    public function isATutor()
    {
        if ($this->role === 'tutor') {
            return true;
        } else {
            return false;
        }
    }

    public function isTheAdmin()
    {
        if ($this->role === 'admin') {
            return true;
        } else {
            return false;
        }
    }
}
