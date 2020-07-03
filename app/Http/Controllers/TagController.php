<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TagController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'tag_name' => 'required|min:3|max:50'
        ]);
        $task = Task::findOrFail($request->task_id);
        if ($task->isOwnedByUser()) {
            $tag = Tag::firstOrCreate([
                'tag_name' => strtolower($request->tag_name)
            ]);
            $task->tags()->syncWithoutDetaching($tag->id);
            return redirect()->route('tasks.show', ['task' => $task]);
        }
        return redirect()
            ->route('tasks.show', ['task' => $task])
            ->with('error', 'You are not authorized perform the request');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @param Request $request
     * @return Response
     */
    public function destroy(Tag $tag, Request $request)
    {
        $task = Task::findOrFail($request->task_id);
        if ($task->isOwnedByUser()) {
            $task->tags()->detach($tag->id);
            return redirect()->route('tasks.show', ['task' => $task]);
        }
        return redirect()
            ->route('tasks.show', ['task' => $task])
            ->with('error', 'You are not authorized perform the request');
    }
}
