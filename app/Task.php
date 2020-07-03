<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    protected $fillable = [
        'title',
        'total_word_count',
        'word_count_break',
        'country',
        'reference_style',
        'description',
        'student_deadline',
        'tutor_deadline',
        'requirement_path',
        'solution_path',
        'upload_date_time',
        'status',
        'created_by',
        'is_allocated_to',
        'allocation_date_time'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function allocatedTo()
    {
        return $this->belongsTo('App\User', 'is_allocated_to');
    }

    public function invitedTutors()
    {
        return $this->belongsToMany('App\User', 'task_invitation')->withTimestamps();
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }

    public function userDiscussions()
    {
        return $this->belongsToMany('App\User', 'discussions')
            ->using('App\Discussion')
            ->withPivot([
                'message',
                'type',
                'created_at'
            ]);
    }

    public function isOwnedByUser()
    {
        if($this->created_by != Auth::user()->id) {
            return false;
        }
        return true;
    }

    public function updateStatus($status)
    {
        /*
         * Supported status
         * unproductive
         * invited
         * allocated
         * uploaded
         * escalated
         * delivered
         * failed
         */
        $this->status = $status;
        $this->save();
    }
}
