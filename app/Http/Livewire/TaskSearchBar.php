<?php

namespace App\Http\Livewire;

use App\Tag;
use App\Task;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TaskSearchBar extends Component
{

    public $query;
    public $tasks;

    public function mount()
    {
        $this->reset();
    }

    public function restore()
    {
        $this->query = '';
        $this->tasks = [];
    }

    public function updatedQuery()
    {
        $this->tasks = [];
        $tags = Tag::where('tag_name', 'like', '%' . $this->query . '%')->orderby('tag_name', 'asc')->pluck('id');
        $this->tasks = Task::join('tag_task', 'tasks.id', '=', 'task_id')
            ->select('tasks.*')
            ->whereIn('tag_id', $tags)
            ->distinct()
            ->get();
    }

    public function render()
    {
        return view('livewire.task-search-bar');
    }
}
