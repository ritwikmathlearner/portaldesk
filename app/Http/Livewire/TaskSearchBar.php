<?php

namespace App\Http\Livewire;

use App\Tag;
use App\Task;
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
        $tag = Tag::where('tag_name', 'like', '%'.$this->query.'%')->orderby('tag_name', 'asc')->first();
        if(!empty($tag)) {
            $this->tasks = $tag->tasks;
        }
    }

    public function render()
    {
        return view('livewire.task-search-bar');
    }
}
