@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-7 p-3">
                <div class="container">
                    <div class="row align-items-center mb-4">
                        <div class="col-12 px-0">
                            <span class="h3 mr-2 my-0">{{ ucfirst($task->title) }}</span>
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
                        <div class="col-12 px-0">
                            @error('escalation_message')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                            @error('fail_message')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                            @error('solution_file')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <br>
                            @error('upload_type')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                @if($task->isOwnedByUser())
                    <div class="d-flex">
                        <div>
                            <button type="button" class="btn btn-warning" data-toggle="modal"
                                    data-target="#escalateModal">
                                Escalate
                            </button>
                            <div>
                                @error('solution_file')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <br>
                                @error('upload_type')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="escalateModal" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Escalate</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form
                                            action="{{ route('tasks.escalate', ['task' => $task]) }}"
                                            method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="escalation_message">Escalation message</label>
                                                    <textarea
                                                        name="escalation_message"
                                                        id="escalation_message"
                                                        rows="4"
                                                        class="form-control"
                                                        placeholder="Required changes"
                                                        required>{{ old('escalation_message') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-warning">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-3">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#failModel">
                                Mark as fail
                            </button>
                            <!-- Modal -->
                            <div class="modal fade" id="failModel" tabindex="-1" role="dialog"
                                 aria-labelledby="failModelLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="failModelLabel">Mark as fail</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form
                                            action="{{ route('tasks.fail', ['task' => $task]) }}"
                                            method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="fail_message">Fail reason</label>
                                                    <textarea
                                                        name="fail_message"
                                                        id="fail_message"
                                                        rows="4"
                                                        class="form-control"
                                                        placeholder="Valid reason for student fail"
                                                        required>{{ old('fail_message') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-danger">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="container my-2">
                    <div class="row align-items-center">
                        <strong class="mr-3">Download requirement file: </strong>
                        <form
                            action="{{ route('tasks.fileDownload', ['task' => $task]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="type" value="requirement">
                            <button type="submit" class="btn btn-success px-4 py-1">
                                <i class="fas fa-file-download"></i>
                            </button>
                        </form>
                        @if($task->isOwnedByUser())
                            <div class="d-flex justify-content-center flex-column align-items-center">
                                <button
                                    type="button"
                                    class="btn text-info"
                                    data-toggle="modal"
                                    data-target="#exampleModal">
                                    <i class="fas fa-file-upload"></i>
                                    <span>Re-upload</span>
                                    @error('requirement_file')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Upload requirement</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form
                                                action="{{ route('tasks.requirementUpload', ['task' => $task]) }}"
                                                method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <div class="custom-file">
                                                            <input type="file" class="custom-file-input"
                                                                   id="requirement_file"
                                                                   name="requirement_file">
                                                            <label class="custom-file-label" for="requirement_file">Choose
                                                                file</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Close
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">Upload</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form
                                action="{{ route('tasks.requirementUpload', ['task' => $task]) }}"
                                method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="solution">

                            </form>
                        @endif
                    </div>
                </div>
                <div class="container my-2">
                    <div class="row align-items-center">
                        <strong class="mr-3">Download solution: </strong>
                        @if($task->solution_path == null)
                            <span class="text-danger">Pending</span>
                        @else
                            <div class="d-flex justify-content-between align-items-center w-25">
                                @if($task->isOwnedByUser() || $task->allocatedTo == \Illuminate\Support\Facades\Auth::user())
                                    <form
                                        action="{{ route('tasks.fileDownload', ['task' => $task]) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="solution">
                                        <button type="submit" class="btn btn-success px-4 py-1">
                                            <i class="fas fa-file-download"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($task->isOwnedByUser())
                                    <form
                                        action="{{ route('tasks.solutionRemove', ['task' => $task]) }}"
                                        method="POST"
                                        onsubmit="return confirm('You want to deallocate tutor from the task?');">
                                        @csrf
                                        <input type="hidden" name="type" value="solution">
                                        <button type="submit" class="btn" title="Remove solution">
                                            <i class="fas fa-trash text-danger"></i>
                                            <span>Remove</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @if($task->upload_date_time == null)
                                <p class="col-12 p-0 m-0 text-danger">Partial uploaded</p>
                            @endif
                        @endif
                    </div>
                </div>
                <p><strong>Word count: </strong> {{ $task->total_word_count }}</p>
                <p><strong>Word count break: </strong> {{ $task->word_count_break }}</p>
                @if($task->isOwnedByUser())
                    <p>
                        <strong>Student deadline: </strong> {{ $task->student_deadline }}
                        ( {{ \Carbon\Carbon::parse($task->student_deadline)->diffForHumans() }} )
                    </p>
                    <p>
                        <strong>Tutor deadline: </strong> {{ $task->tutor_deadline }}
                        ( {{ \Carbon\Carbon::parse($task->tutor_deadline)->diffForHumans() }} )
                    </p>
                @else
                    <p>
                        <strong>Deadline: </strong> {{ $task->tutor_deadline }}
                        ( {{ \Carbon\Carbon::parse($task->tutor_deadline)->diffForHumans() }} )
                    </p>
                @endif
                <p><strong>Reference style: </strong>{{ $task->reference_style }}</p>
                <p><strong>Country: </strong>{{ $task->country }}</p>
                <p><strong>Description: </strong>{{ $task->description }}</p>
                @if($task->isOwnedByUser())
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <form action="{{ route('tags.store', ['task' => $task]) }}" method="POST">
                                    @csrf
                                    <div class="form-row align-items-center">
                                        <label for="tag_name" class="col-md-4">Tag name</label>
                                        <input
                                            type="text"
                                            class="form-control col-md-8"
                                            name="tag_name"
                                            id="tag_name"
                                            value="{{ old('tag_name') }}"
                                            required>
                                        @error('tag_name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="task_id" id="task_id" value="{{ $task->id }}">
                                </form>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <p><strong>Assigned tags: </strong></p>
                                <div>
                                    @forelse($task->tags as $tag)
                                        <form
                                            method="POST"
                                            action="{{ route('tags.destroy', ['tag' => $tag]) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="task_id" value="{{ $task->id }}">
                                            <button type="submit"
                                                    class="btn px-2 py-1 rounded-pill m-1 border border-info text-info">
                                                {{ $tag->tag_name }}
                                                <i class="fas fa-times text-danger ml-1"></i>
                                            </button>
                                        </form>
                                    @empty
                                        <p class="text-danger">No tags are assigned</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="container d-block d-md-none w-100">
                <hr>
            </div>
            <div class="col-md-4 offset-md-1">
                @if($task->isOwnedByUser())
                    <div class="row p-3 text-center">
                        <div class="col">
                            <a href="{{ route('tasks.edit', ['task' => $task]) }}" class="btn btn-primary w-75">Edit</a>
                        </div>
                        <div class="col">
                            <form
                                method="POST"
                                action="{{ route('tasks.destroy', ['task' => $task]) }}"
                                onsubmit="return deleteTask(event)"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-75">DELETE</button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col">
                            <form
                                method="POST"
                                action="{{ route('tasks.changeStatus', ['task' => $task]) }}"
                            >
                                @csrf
                                <div class="form-group">
                                    <label for="status">
                                        Status
                                        <small class="text-secondary">(force change status)</small>
                                    </label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option selected disabled>Select status...</option>
                                        <option value="unproductive">Unproductive</option>
                                        <option value="invited">Invited</option>
                                        <option value="allocated">Allocated</option>
                                        <option value="uploaded">Uploaded</option>
                                        <option value="escalated">Escalated</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                    @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Change</button>
                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3 p-3 align-items-center">
                        @if($task->is_allocated_to == NULL)
                            <form class="w-100" method="POST"
                                  action="{{ route('tasks.allocate', ['task' => $task->id]) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Allocate</label>
                                    <input
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        id="email"
                                        placeholder="Enter email address of tutor"
                                        required>
                                    @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success">Allocate</button>
                            </form>
                        @else
                            <div class="row col-md-8 m-0 p-0 align-items-center">
                                <strong>Allocated to: </strong>
                                <form class="" method="POST"
                                      action="{{ route('tasks.deallocate', ['task' => $task->id]) }}"
                                      onsubmit="return confirm('You want to deallocate tutor from the task?');"
                                >
                                    @csrf
                                    <button type="submit"
                                            class="btn px-2 py-1 rounded-pill ml-2 border border-success text-success">
                                        {{ $task->allocatedTo->name }}
                                        <i class="fas fa-times text-danger ml-1"></i>
                                    </button>
                                </form>
                                <span>
                                    <strong>On: </strong>
                                    {{ \Carbon\Carbon::parse($task->allocation_date_time)->toDayDateTimeString() }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <hr>
                    <div>
                        <div class="container">
                            <div class="row align-items-center mb-3">
                                <p class="m-0 p-0 mr-1"><strong>Invited to: </strong>
                                @forelse($task->invitedTutors as $tutor)
                                    <form
                                        method="POST"
                                        action="{{ route('tasks.deinvite', ['task' => $task]) }}"
                                        class="d-inline border border-info rounded-pill py-0 pl-2 m-1"
                                    >
                                        @csrf
                                        <input type="hidden" name="tutorId" value="{{ $tutor->id }}">
                                        <button
                                            class="btn p-0 text-info"
                                            onclick="event.preventDefault()"
                                            data-clipboard-text="{{ $tutor->email }}"
                                            title="Copy email address">
                                            {{ $tutor->name }}
                                        </button>
                                        <button type="submit" class="btn" title="Remove invitation">
                                            <i class="fas fa-times text-danger"></i>
                                        </button>
                                    </form>
                                @empty
                                    <span class="text-danger">No one</span>
                                    @endforelse
                                    </p>
                            </div>
                            <div class="row">
                                <form class="w-100" method="POST"
                                      action="{{ route('tasks.invite', ['task' => $task->id]) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="tutors">Invite Tutors</label>
                                        <textarea
                                            name="tutors"
                                            id="tutors"
                                            class="form-control"
                                            rows="5"
                                            placeholder="Use comma(,) to separate tutor emails"
                                            required></textarea>
                                        @if(session()->has('notatutor'))
                                            <small class="text-danger">{!! session()->get('notatutor') !!}</small>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-info text-light">Invite</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    @if($task->allocatedTo == \Illuminate\Support\Facades\Auth::user())
                        <div class="d-flex justify-content-center flex-column align-items-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#exampleModal">
                                Upload solution
                            </button>
                            <div>
                                @error('solution_file')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                                <br>
                                @error('upload_type')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Upload solution</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form
                                            action="{{ route('tasks.solutionUpload', ['task' => $task]) }}"
                                            method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="form-group">

                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio"
                                                               name="upload_type" id="complete" value="complete">
                                                        <label class="form-check-label" for="complete">Complete</label>
                                                    </div>
                                                    @if($task->upload_date_time == null)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                   name="upload_type" id="partial" value="partial">
                                                            <label class="form-check-label"
                                                                   for="partial">Partial</label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="solution_file"
                                                               name="solution_file" required>
                                                        <label class="custom-file-label" for="solution_file">Choose
                                                            file</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" class="btn btn-primary">Upload solution</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
            <hr>
            <div class="container" id="discussion">
                <div class="row align-items-center p-3 mt-3 border rounded bg-secondary text-light">
                    <div class="col-12">
                        <h1 class="text-center">Discussions</h1>
                    </div>
                    <div
                        class="col-12 col-md-8 bg-white text-secondary rounded p-3"
                        style="height: 50vh; overflow-y: auto; overflow-x: hidden">
                        <div class="d-flex justify-content-start flex-column-reverse">
                            @if($task->isOwnedByUser())
                                @foreach($task->userDiscussions as $message)
                                    <div class="p-2 text-light m-0 my-2 rounded
                                {{ $message->pivot->user_id == \Illuminate\Support\Facades\Auth::user()->id ? 'align-self-end bg-success' : 'align-self-start bg-primary'}}
                                        "
                                         style="max-width: 90%;">
                                        <small
                                            class="d-inline-block
                                             {{ $message->pivot->type == 'escalation' || $message->pivot->type == 'fail' ? 'bg-danger' : 'bg-dark'}}
                                                text-light px-2 py-1 mb-2 rounded-pill">{{ ucfirst($message->pivot->type) }}</small>
                                        <span class="d-block">{{ $message->pivot->message }}</span>
                                        @if(\Illuminate\Support\Facades\Auth::user()->id != $message->pivot->user_id)
                                            <small><i>By: </i>
                                                <span
                                                    class="btn btn-warning rounded-pill p-1"
                                                    data-clipboard-text="{{ App\User::find($message->pivot->user_id)->email }}"
                                                    title="Copy email address">
                                                {{ App\User::find($message->pivot->user_id)->name }}
                                            </span>
                                            </small>
                                        @else
                                            <small>You</small>
                                        @endif
                                        <small><i>On: </i>{{ \Carbon\Carbon::parse($message->pivot->created_at)->toDayDateTimeString() }}
                                        </small>

                                    </div>
                                @endforeach
                            @else
                                @foreach($task->userDiscussions as $message)
                                    @if($message->pivot->user_id == \Illuminate\Support\Facades\Auth::user()->id || $message->pivot->user_id == $task->user->id)
                                        <div class="p-2 text-light
                                    {{ $message->pivot->user_id == \Illuminate\Support\Facades\Auth::user()->id ? 'align-self-end bg-success' : 'align-self-start bg-primary'}}
                                            m-0 my-2 rounded"
                                             style="max-width: 90%;">
                                            <small
                                                class="d-inline-block
                                             {{ $message->pivot->type == 'escalation' || $message->pivot->type == 'fail' ? 'bg-danger' : 'bg-dark'}}
                                                    text-light px-2 py-1 mb-2 rounded-pill">{{ ucfirst($message->pivot->type) }}</small>
                                            <span class="d-block">{{ $message->pivot->message }}</span>
                                            @if(\Illuminate\Support\Facades\Auth::user()->id != $message->pivot->user_id)
                                                <small><i>By: </i>
                                                    <span
                                                        class="btn btn-warning rounded-pill p-1"
                                                        data-clipboard-text="{{ App\User::find($message->pivot->user_id)->email }}"
                                                        title="Copy email address">
                                                    {{ App\User::find($message->pivot->user_id)->name }}
                                                </span>
                                                </small>
                                            @else
                                                <small>You</small>
                                            @endif
                                            <small><i>On: </i>{{ \Carbon\Carbon::parse($message->pivot->created_at)->toDayDateTimeString() }}
                                            </small>

                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-4 px-0 px-md-3 mt-3 mt-md-0">
                        <form
                            action="{{ route('tasks.message', ['task' => $task]) }}"
                            method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="message">Your message</label>
                                <textarea
                                    name="message"
                                    id="message"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Enter your message here"
                                    required>{{ old('message') }}</textarea>
                                @error('message')
                                <small class="text-warning">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="type">Message type</label>
                                <select name="type" id="type" class="form-control">
                                    <option disabled selected>Select type...</option>
                                    <option value="reply">Reply</option>
                                    <option value="clarification">Clarification</option>
                                    <option value="extra word">Extra word</option>
                                    <option value="confirmation">Confirmation</option>
                                    <option value="progress">Progress update</option>
                                </select>
                                @error('type')
                                <small class="text-warning">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="d-flex">
                                <button class="btn btn-success w-50 mx-auto">
                                    Send
                                    <span class="ml-3">
                                    <i class="fas fa-paper-plane"></i>
                                </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
