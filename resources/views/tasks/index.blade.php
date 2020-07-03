@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3 px-3">
                <h4>Filter tasks</h4>
                <form method="POST" action="">
                    @csrf
                    <div class="form-row">
                        <label class="col-12" for="allocateEndDate">Deadline between</label>
                        <div class="col">
                            <input type="date" class="form-control" id="dlStartDate" name="dlStartDate">
                        </div>
                        <div class="col">
                            <input type="date" class="form-control" id="dlEndDate" name="dlEndDate">
                        </div>
                    </div>
                    <hr>
                    <div class="form-row mb-3">
                        <label class="col-12" for="minWordCount">Word count</label>
                        <div class="col">
                            <label for="allocateStartDate">Min</label>
                            <input type="number" class="form-control" id="minWordCount" name="minWordCount">
                        </div>
                        <div class="col">
                            <label for="allocateStartDate">Max</label>
                            <input type="number" class="form-control" id="maxWordCount" name="maxWordCount">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <ul class="list-group col-md-9 px-3 px-md-0 mt-3 mt-md-0">
                @forelse($tasks as $task)
                    <li class="list-group-item">
                        <div class="container px-3 mb-3 mb-md-4">
                            <div class="row justify-content-start align-items-center px-md-0 px-3">
                                <a href="{{ route('tasks.show', ['task' => $task]) }}" class="text-dark mr-2">
                                    <h5 class="h5 font-weight-bold mb-0">{{ $task->title }}</h5>
                                </a>
                                <small
                                    class="text-light
                                    <?php
                                    echo $task->status == 'failed' ? 'bg-danger' : ($task->status == 'unproductive' ? 'bg-secondary' : ($task->status == 'delivered' ? 'bg-success' : 'bg-primary'))
                                    ?>
                                        px-2 py-1 rounded">
                                    {{ ucfirst($task->status) }}
                                </small>
                            </div>
                        </div>
                        <div class="container">
                            <div class="row">
                                @if($task->isOwnedByUser())
                                    <div class="col-md">
                                        <div class="container">
                                            <div class="row">

                                                <p class="p-0 m-0">
                                                    <strong>Student dl: </strong>
                                                    <span
                                                        class="text-dark ml-2">{{ \Carbon\Carbon::parse($task->student_deadline)->diffForHumans() }}</span>
                                                </p>
                                                <p class="p-0 m-0">
                                                    <strong>Tutor dl: </strong>
                                                    <span
                                                        class="text-dark ml-2">{{ \Carbon\Carbon::parse($task->tutor_deadline)->diffForHumans() }}</span>
                                                </p>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md">
                                    @if(\Illuminate\Support\Facades\Auth::user()->isATutor())
                                        <p class="p-0 m-0">
                                            <strong>Uploaded by: </strong>
                                            <span>{{ $task->user->name }}</span>
                                        </p>
                                    @endif
                                    <p class="m-0 p-0">
                                        <strong>Total word count: </strong>
                                        <span>{{ $task->total_word_count }} words</span>
                                    </p>
                                    <p class="m-0 p-0">
                                        <strong>Upload: </strong>
                                        @if($task->solution_path != null)
                                            @if($task->upload_date_time != null)
                                                <span>Complete</span>
                                            @else
                                                <span>Partial</span>
                                            @endif
                                        @else
                                            <span>Pending</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md my-3 my-md-0">
                                    @if($task->isOwnedByUser())
                                        @if($task->is_allocated_to == NULL)
                                            <p class="text-danger  m-0 p-0">Task is not allocated</p>
                                        @else
                                            <p class="col-12 m-0 p-0">
                                                Task is allocated to:
                                                <span class="text-primary">{{ $task->allocatedTo->name }}</span>
                                            </p>
                                        @endif
                                        <p class="col-12 m-0 p-0">
                                            Task is invited to:
                                            <span class="text-primary">{{ count($task->invitedTutors) }}</span>
                                            tutor(s)
                                        </p>
                                    @else
                                        @if($task->is_allocated_to != NULL)
                                            <p class="col-12 m-0 p-0">
                                                Allocated at:
                                                <span
                                                    class="text-primary">{{ \Carbon\Carbon::parse($task->allocation_date_time)->toDayDateTimeString() }}</span>
                                            </p>
                                        @endif
                                        <p class="col-12 m-0 p-0">
                                            Deadline:
                                            <span class="text-primary">
                                                    {{ \Carbon\Carbon::parse($task->tutor_deadline)->toDayDateTimeString() }}
                                                    ( {{ \Carbon\Carbon::parse($task->tutor_deadline)->diffForHumans() }} )
                                                </span>
                                        </p>
                                    @endif
                                </div>
                                @if($task->isOwnedByUser())
                                    <div class="col">
                                        <div class="d-flex justify-content-around">
                                            <div class="">
                                                <a href="{{ route('tasks.edit', ['task' => $task]) }}"
                                                   class="btn btn-primary">Edit</a>
                                            </div>
                                            <div class="">
                                                <form
                                                    method="POST"
                                                    action="{{ route('tasks.destroy', ['task' => $task]) }}"
                                                    onsubmit="return deleteTask(event)"
                                                    class="w-100"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">DELETE</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-danger list-group-item">No tasks are available</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection
