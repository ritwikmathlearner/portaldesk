<div>
    <input
        class="form-control mr-sm-2"
        type="search"
        placeholder="Enter tags"
        aria-label="Search"
        wire:model="query"
        name="tag"
        id="tag"
        required>

    @if(!empty($query))
        <div style="position: fixed; top: 10vh; right: 0; bottom: 0; left: 0; z-index: 2" wire:click="restore"></div>
        <ul class="list-group" style="position: absolute; top: 105%; left: 50%; transform: translate(-50%, 0); width: 80vw; z-index: 10">
            @forelse($tasks as $task)
                <li class="list-group-item">
                    <div class="container px-3 mb-3 mb-md-4">
                        <div class="row justify-content-start align-items-center px-md-0 px-3">
                            <a href="{{ route('tasks.show', ['task' => $task]) }}" class="text-dark mr-2">
                                <h5 class="h5 font-weight-bold mb-0">{{ $task->title }}</h5>
                            </a>
                            <small
                                class="border
                                    {{ $task->status == 'failed'
                                        ? 'border-danger text-danger' : ($task->status == 'unproductive'
                                        ? 'border-secondary text-secondary' : ($task->status == 'delivered'
                                        ? 'border-success text-success' : 'border-primary text-primary')) }}
                                    px-2 py-1 rounded-pill">
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
                                @if($task->is_allocated_to == NULL)
                                    <p class="text-danger  m-0 p-0">Task is not allocated</p>
                                    <p class="col-12 m-0 p-0">
                                        Task is invited to:
                                        <span class="text-primary">{{ count($task->invitedTutors) }}</span>
                                        tutor(s)
                                    </p>
                                @else
                                    @if($task->isOwnedByUser())
                                        <p class="col-12 m-0 p-0">
                                            Task is allocated to:
                                            <span class="text-primary">{{ $task->allocatedTo->name }}</span>
                                        </p>
                                        <p class="col-12 m-0 p-0">
                                            Task is invited to:
                                            <span class="text-primary">{{ count($task->invitedTutors) }}</span>
                                            tutor(s)
                                        </p>
                                    @else
                                        <p class="col-12 m-0 p-0">
                                            Allocated at:
                                            <span class="text-primary">{{ \Carbon\Carbon::parse($task->allocation_date_time)->toDayDateTimeString() }}</span>
                                        </p>
                                        <p class="col-12 m-0 p-0">
                                            Deadline:
                                            <span class="text-primary">
                                                    {{ \Carbon\Carbon::parse($task->tutor_deadline)->toDayDateTimeString() }}
                                                    ( {{ \Carbon\Carbon::parse($task->tutor_deadline)->diffForHumans() }} )
                                                </span>
                                        </p>
                                    @endif
                                @endif
                            </div>
                            @if($task->isOwnedByUser())
                                <div class="col">
                                    <div class="d-flex justify-content-around">
                                        <div class="">
                                            <a href="{{ route('tasks.edit', ['task' => $task]) }}" class="btn btn-primary">Edit</a>
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
                <li class="list-group-item"><span class="text-danger">No task found</span></li>
            @endforelse
        </ul>
    @endif
</div>
