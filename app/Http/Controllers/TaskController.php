<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{

    /**
     * @var Authenticatable|null
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAnOwner()) {
            $tasks = Task::where('created_by', Auth::user()->id)->orderBy('student_deadline', 'desc')->paginate(5);
        } elseif (Auth::user()->isATutor()) {
            $userId = Auth::user()->id;
            $tasks = Task::join('task_invitation', 'tasks.id', '=', 'task_id')
                ->where('is_allocated_to', '=', $userId)
                ->orWhere(function ($query) {
                    $query->where('task_invitation.user_id', Auth::user()->id)
                        ->whereNull('is_allocated_to');
                })
                ->select('tasks.*')
                ->orderBy('tutor_deadline')
                ->distinct()
                ->paginate(5);
        } elseif (Auth::user()->isTheAdmin()) {
            $tasks = Task::all();
        }
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public
    function create()
    {
        if (Auth::user()->isAnOwner()) {
            return view('tasks.create');
        }
        return redirect()->route('tasks.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public
    function store(Request $request)
    {
        if (Auth::user()->isAnOwner()) {
            $validatedData = $request->validate([
                'title' => 'required',
                'total_word_count' => 'required|integer',
                'word_count_break' => 'required',
                'country' => '',
                'reference_style' => '',
                'description' => 'required|max:1000',
                'student_deadline' => 'required|date|after:now',
                'tutor_deadline' => 'required|date|before:student_deadline|after:now',
                'requirement_file' => 'required|mimes:zip,rar|max:20480'
            ]);
            $validatedData['created_by'] = Auth::user()->id;
            $validatedData['student_deadline'] = Carbon::parse($request->student_deadline)->format('Y-m-d H:i:s');
            $validatedData['tutor_deadline'] = Carbon::parse($request->tutor_deadline)->format('Y-m-d H:i:s');
            $extension = $request->file('requirement_file')->extension();
            $validatedData['requirement_path'] = Storage::putFileAs(
                'requirements',
                $request->file('requirement_file'),
                "{$request->title}_{$validatedData['created_by']}_" . time() . '.' . $extension
            );
            $task = Task::create($validatedData);
            $tag = Tag::firstOrCreate([
                'tag_name' => $task->title
            ]);
            $task->tags()->attach($tag->id);
            return redirect()->route('tasks.show', ['task' => $task])->with('toast_success', 'Task added');
        }
        return redirect()->route('tasks.index')->with('warning', 'You are not authorized to access the task');
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @param Request $request
     * @return Response
     */
    public
    function show(Task $task, Request $request)
    {
        if ($task->isOwnedByUser()) {
            return view('tasks.show', ['task' => $task]);
        }
        if ($task->allocatedTo == Auth::user()) {
            return view('tasks.show', ['task' => $task]);
        }
        if ($task->is_allocated_to == null && $this->isTutorInvited(Auth::user()->email, $task)) {
            return view('tasks.show', ['task' => $task]);
        }
        return redirect()->route('tasks.index')->with('warning', 'You are not authorized to access the task');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return Response
     */
    public
    function edit(Task $task)
    {
        if ($task->isOwnedByUser()) {
            return view('tasks.edit', ['task' => $task]);
        }
        return redirect()
            ->route('tasks.index')
            ->with('warning', 'Your are not authorized to perform this request');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public
    function update(Request $request, Task $task)
    {
        if ($task->isOwnedByUser()) {
            $validatedData = $request->validate([
                'title' => 'required',
                'total_word_count' => 'required|integer',
                'word_count_break' => 'required',
                'country' => '',
                'reference_style' => '',
                'description' => 'required|max:1000',
                'student_deadline' => 'required|date|after:now',
                'tutor_deadline' => 'required|date|before:student_deadline|after:now'
            ]);
            $validatedData['student_deadline'] = Carbon::parse($request->student_deadline)->format('Y-m-d H:i:s');
            $validatedData['tutor_deadline'] = Carbon::parse($request->tutor_deadline)->format('Y-m-d H:i:s');
            Task::where('id', $task->id)
                ->update($validatedData);
            return redirect()->route('tasks.show', ['task' => $task]);
        }
        return redirect()
            ->route('tasks.index')
            ->with('warning', 'Your are not authorized to perform this request');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return Response
     */
    public
    function destroy(Task $task)
    {
        if ($task->isOwnedByUser()) {
            $task = Task::destroy($task->id);
            if ($task != 0) {
                return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
            }
            return redirect()->route('tasks.index')->with('warning', 'Task delete request failed');
        }
        return redirect()
            ->route('tasks.index')
            ->with('warning', 'Your are not authorized to perform this request');
    }

    /**
     * Allocate task to tutors
     *
     * @param Task $task
     * @param Request $request
     * @return Request
     */
    public
    function allocate(Task $task, Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|exists:users,email',
            'id' => 'exists:tasks'
        ]);
        if ($task->isOwnedByUser()) {
            $tutorId = User::where('email', $validatedData['email'])->first()->id;
            if ($tutorId != Auth::user()->id) {
                $task->is_allocated_to = $tutorId;
                $task->allocation_date_time = now();
                $task->save();
                $this->statusOperation($task);
                $request->session()->flash('success', 'Task allocation is successful');
                return redirect()->route('tasks.show', ['task' => $task]);
            } else {
                $request->session()->flash('error', 'You cannot assign task to yourself');
                return redirect()->route('tasks.show', ['task' => $task]);
            }
        }
        $request->session()->flash('warning', 'You are not authorized perform the request');
        return redirect()->route('tasks.index');
    }

    /**
     * Remove tutor allocated to the task
     *
     * @param Task $task
     * @param Request $request
     * @return Request
     */
    public
    function deallocate(Task $task, Request $request)
    {
        if ($task->isOwnedByUser()) {
            $task->is_allocated_to = null;
            $task->allocation_date_time = null;
            $task->save();
            $this->statusOperation($task);
            $request->session()->flash('success', 'Task deallocate is successful');
            return redirect()->route('tasks.show', ['task' => $task]);
        }
        $request->session()->flash('error', 'You are not authorized perform the request');
        return redirect()->route('tasks.index');
    }

    /**
     * Invite one or more tutors to check task
     *
     * @param Task $task
     * @param Request $request
     * @return Request
     */
    public
    function invite(Task $task, Request $request)
    {
        $request->tutors = explode(',', $request->tutors);
        $invalidEmails = '';
        $userIds = [];
        foreach ($request->tutors as $email) {
            if (User::where('email', trim($email))->first() == null || User::where('email', trim($email))->first()->role == 'owner') {
                $invalidEmails .= "$email ";
            } else {
                array_push($userIds, User::where('email', trim($email))->first()->id);
            }
        }
        if ($invalidEmails == '') {
            $task->invitedTutors()->syncWithoutDetaching($userIds);
            $this->statusOperation($task);
            $request->session()->flash('success', "Invited all the tutors successfully");
            return redirect()->route('tasks.show', ['task' => $task]);
        } else {
            $request->session()->flash('notatutor', "Entered email(s) <u>{$invalidEmails}</u> is/are not valid tutor(s)");
            return redirect()->route('tasks.show', ['task' => $task]);
        }
    }

    /**
     * Remove task invitation
     *
     * @param Task $task
     * @param Request $request
     * @return Request
     */
    public
    function deinvite(Task $task, Request $request)
    {
        if($task->isOwnedByUser()){
            $task->invitedTutors()->detach($request->tutorId);
            $this->statusOperation($task);
            return redirect()->route('tasks.show', ['task' => $task])->with('toast_success', 'Invitation removed');
        }
        return back()->with('error', 'You are not authorized perform the request');
    }

    /**
     * Remove task invitation
     *
     * @param Task $task
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public
    function fileDownload(Task $task, Request $request)
    {
        if (
            $request->type == 'requirement'
            &&
            (
                $this->isTutorInvited(Auth::user()->email, $task)
                ||
                $task->isOwnedByUser()
                ||
                $task->allocatedTo() == Auth::user()
            )
        ) {
            $fileName = str_replace("requirements/", "", $task->requirement_path);
            $path = "{$task->requirement_path}";
        } elseif (
            $request->type == 'solution'
            &&
            (
                $task->isOwnedByUser()
                ||
                $task->allocatedTo == Auth::user()
            )
        ) {
            $fileName = str_replace("solutions/", "", $task->solution_path);
            $path = "{$task->solution_path}";
            if ($task->isOwnedByUser() && $task->upload_date_time != null) {
                $this->statusOperation($task, 'delivered');
            }
        } else {
            return redirect()->route('tasks.show', ['task' => $task])->with('warning', 'Requested requirement file is from another task');
        }
        if (Storage::exists($path)) {
            $file = Storage::get($path);
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-disposition' => "attachment; filename={$fileName}"
            ];
            return \response($file, 200, $headers);
        } else {
            $request->session()->flash('error', 'Requested file does not exists');
            return redirect()->route('tasks.show', ['task' => $task]);
        }

    }

    /**
     * Check if user with passed email is invited to check the task or not
     *
     * @param Task $task
     * @param Request $request
     * @return bool
     */
    public
    function solutionUpload(Task $task, Request $request)
    {
        $validatedData = $request->validate([
            'upload_type' => 'required|in:complete,partial',
            'solution_file' => 'required|mimes:zip,rar|max:20480'
        ]);
        if ($task->allocatedTo == Auth::user()) {
            if ($task->solution_path != null) {
                Storage::delete($task->solution_path);
            }
            $extension = $request->file('solution_file')->extension();
            $task->solution_path = Storage::putFileAs(
                'solutions',
                $request->file('solution_file'),
                $task->title . '_' . time() . '.' . $extension
            );
            if ($request->upload_type == 'complete') {
                $task->upload_date_time = now();
            }
            $task->save();
            $this->statusOperation($task);
            return redirect()->route('tasks.show', ['task' => $task])->with('success', 'Task uploaded successfully');
        }
        return redirect()->route('tasks.show', ['task' => $task])->with('warning', 'Task is not allocated to you');
    }

    public function solutionRemove(Task $task, Request $request)
    {
        if ($task->isOwnedByUser()) {
            Storage::delete($task->solution_path);
            $task->solution_path = null;
            $task->upload_date_time = null;
            $task->save();
            $this->statusOperation($task);
            return redirect()->route('tasks.show', ['task' => $task]);
        }
        $request->session()->flash('error', 'You are not authorized to perform the request');
        return redirect()->route('tasks.show', ['task' => $task]);
    }

    /**
     * Check if user with passed email is invited to check the task or not
     *
     * @param Task $task
     * @param Request $request
     * @return bool
     */
    public function requirementUpload(Task $task, Request $request)
    {
        if ($task->isOwnedByUser()) {
            $validatedData = $request->validate([
                'requirement_file' => 'required|mimes:zip,rar|max:20480'
            ]);
            Storage::delete($task->requirement_path);
            $extension = $request->file('requirement_file')->extension();
            $task->requirement_path = Storage::putFileAs(
                'requirements',
                $request->file('requirement_file'),
                "{$task->title}_{$task->created_by}_" . time() . '.' . $extension
            );
            $task->save();
            return redirect()
                ->route('tasks.show', ['task' => $task])
                ->with('success', 'Requirement file uploaded successfully');
        }
        return back()->with('warning', 'You are not allowed to perform this request');
    }

    public function changeStatus(Task $task, Request $request)
    {
        $request->validate([
            'status' => 'required|in:unproductive,invited,allocated,uploaded,escalated,delivered,failed'
        ]);
        if ($task->isOwnedByUser()) {
            $this->statusOperation($task, $request->status, true);
            return redirect()
                ->route('tasks.show', ['task' => $task])
                ->with('success', 'Status changed successfully');
        }
    }

    public function escalate(Task $task, Request $request)
    {
        $validatedData = $request->validate([
            'escalation_message' => 'required|max:500|min:10'
        ]);
        if ($task->isOwnedByUser()) {
            $user = $task->user;
            $this->statusOperation($task, 'escalated', true);
            $task->userDiscussions()->attach($user->id, [
                'message' => $validatedData['escalation_message'],
                'type' => 'escalation'
            ]);
            return redirect()
                ->route('tasks.show', ['task' => $task])
                ->with('success', 'Escalation submitted successfully');
        }
        return back()->with('warning', 'You are not allowed to perform this request');
    }

    public function fail(Task $task, Request $request)
    {
        $validatedData = $request->validate([
            'fail_message' => 'required|max:500|min:10'
        ]);
        if ($task->isOwnedByUser()) {
            $user = $task->user;
            $this->statusOperation($task, 'failed', true);
            $task->userDiscussions()->attach($user->id, [
                'message' => $validatedData['fail_message'],
                'type' => 'fail'
            ]);
            return redirect()
                ->route('tasks.show', ['task' => $task])
                ->with('success', 'Task is marked as failed successfully');
        }
        return back()->with('warning', 'You are not allowed to perform this request');
    }

    public function storeMessage(Task $task, Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required|max:500|min:5',
            'type' => 'required|in:reply,clarification,confirmation,extra word'
        ]);
        $user = Auth::user();
        $invitedUsers = $task->invitedTutors()->pluck('user_id')->toArray();
        if ($task->isOwnedByUser() || $task->allocatedTo() == $user || in_array($user->id, $invitedUsers)) {
            $task->userDiscussions()->attach($user->id, [
                'message' => $validatedData['message'],
                'type' => $validatedData['type']
            ]);
            return redirect()
                ->route('tasks.show', ['task' => $task])->with('toast_success', 'Message sent');
        } else {
            return back()->with('warning', 'You are not allowed to perform this request');
        }
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'dlStartDate' => 'date',
            'dlEndDate' => 'date|after:dlStartDate',
            'minWordCount' => '',
            'maxWordCount' => '',
            'status' => 'required|in:unproductive,missed,invited,allocated,completed,escalated,failed',
        ]);
        $tasks = Task::query();
        $query = "Searching for <span style='text-decoration: underline; font-style: italic'>";
        if (Auth::user()->isATutor()) {
            if ($validatedData['status'] == 'invited') {
                $tasks->join('task_invitation', 'tasks.id', '=', 'task_id')
                    ->whereNull('is_allocated_to')
                    ->where('task_invitation.user_id', Auth::user()->id)
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " invited";
            }
            if ($validatedData['status'] == 'allocated') {
                $tasks->where('is_allocated_to', Auth::user()->id)
                    ->where('tutor_deadline', '>', now())
                    ->where('status', '=', 'allocated')
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " allocated";
            }
            if ($validatedData['status'] == 'missed') {
                $tasks->where('is_allocated_to', Auth::user()->id)
                    ->where('tutor_deadline', '<', now())
                    ->where('status', '=', 'allocated')
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " deadline missed";
            }
            if ($validatedData['status'] == 'escalated') {
                $tasks->where('is_allocated_to', Auth::user()->id)
                    ->where('status', '=', 'escalated')
                    ->whereNotNull('upload_date_time')
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " escalated";
            }
            if ($validatedData['status'] == 'completed') {
                $tasks->where('is_allocated_to', Auth::user()->id)
                    ->whereNotNull('upload_date_time')
                    ->where(function ($query) {
                        $query->where('status', '=', 'uploaded')
                            ->orWhere('status', '=', 'delivered');
                    })
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " completed";
            }
            if ($validatedData['status'] == 'failed') {
                $tasks->where('is_allocated_to', Auth::user()->id)
                    ->whereNotNull('upload_date_time')
                    ->where('status', '=', 'failed')
                    ->orderBy('tutor_deadline', 'desc')
                    ->select('tasks.*')
                    ->distinct();
                $query .= " failed";
            }
            $query .= "</span> tasks";
            if ($validatedData['dlStartDate'] != null) {
                $tasks->where('tutor_deadline', '>', $validatedData['dlStartDate']);
                $query .= " <span style='text-decoration: underline; font-style: italic'>min:deadline</span> " . $validatedData['dlStartDate'];
            }
            if ($validatedData['dlEndDate'] != null) {
                $tasks->where('tutor_deadline', '<', $validatedData['dlEndDate']);
                $query .= " <span style='text-decoration: underline; font-style: italic'>max:deadline</span> " . $validatedData['dlEndDate'];
            }
        }
        if (Auth::user()->isAnOwner()) {
            $tasks->where('status', '=', $validatedData['status']);
            $query .= " {$validatedData['status']}";
            $query .= "</span> tasks";
            if ($validatedData['dlStartDate'] != null) {
                $tasks->where('student_deadline', '>', $validatedData['dlStartDate']);
                $query .= " <span style='text-decoration: underline; font-style: italic'>min:deadline</span> " . $validatedData['dlStartDate'];
            }
            if ($validatedData['dlEndDate'] != null) {
                $tasks->where('student_deadline', '<', $validatedData['dlEndDate']);
                $query .= " <span style='text-decoration: underline; font-style: italic'>max:deadline</span> " . $validatedData['dlEndDate'];
            }
        }
        if ($validatedData['minWordCount'] != null) {
            $tasks->where('total_word_count', '>', $validatedData['minWordCount']);
            $query .= " <span style='text-decoration: underline; font-style: italic'>min:word count</span> " . $validatedData['minWordCount'] . " words";
        }
        if ($validatedData['maxWordCount'] != null) {
            $tasks->where('total_word_count', '<', $validatedData['maxWordCount']);
            $query .= " <span style='text-decoration: underline; font-style: italic'>max:word count</span> " . $validatedData['maxWordCount'] . " words";
        }
        $tasks = $tasks->get();
        $data = [
            'tasks' => $tasks,
            'query' => $query
        ];
        return view('tasks.search', ['data' => $data]);
    }

    /**
     * Check if user with passed email is invited to check the task or not
     *
     * @param $email
     * @param Task $task
     * @return bool
     */
    public function isTutorInvited($email, $task)
    {
        $userId = User::where('email', $email)->first()->id;
        $success = false;
        foreach ($task->invitedTutors as $tutor) {
            if ($tutor->id == $userId) {
                $success = true;
            }
        }
        if ($success) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param Task $task
     * @param null $status
     * @param $force
     * @return void
     */
    public function statusOperation(Task $task, $status = null, $force = false)
    {
        if ($force) {
            if ($status == 'unproductive') {
                $task->updateStatus($status);
            } elseif ($status == 'invited') {
                $task->updateStatus($status);
            } elseif ($status == 'allocated') {
                $task->updateStatus($status);
            } elseif ($status == 'uploaded') {
                $task->updateStatus($status);
            } elseif ($status == 'escalated') {
                $task->updateStatus($status);
            } elseif ($status == 'failed') {
                $task->updateStatus($status);
            } elseif ($status == 'uploaded') {
                $task->updateStatus($status);
            } elseif ($status == 'delivered') {
                $task->updateStatus($status);
            }
        } elseif ($task->status != 'failed') {
            if (count($task->invitedTutors) == 0 && $task->is_allocated_to == null) {
                $task->updateStatus('unproductive');
            } elseif (count($task->invitedTutors) > 0 && $task->status != 'invited' && $task->is_allocated_to == null && $task->upload_date_time == null) {
                $task->updateStatus('invited');
            } elseif ($task->is_allocated_to != null && $task->status != 'allocated' && $task->upload_date_time == null) {
                $task->updateStatus('allocated');
            } elseif ($status == 'unproductive') {
                $task->updateStatus($status);
            } elseif ($status == 'invited') {
                $task->updateStatus($status);
            } elseif ($status == 'allocated') {
                $task->updateStatus($status);
            } elseif ($status == 'uploaded') {
                $task->updateStatus($status);
            } elseif ($status == 'escalated') {
                $task->updateStatus($status);
            } elseif ($status == 'failed') {
                $task->updateStatus($status);
            } elseif ($status == 'delivered') {
                $task->updateStatus($status);
            } elseif ($task->solution_path != null && $task->status != 'delivered' && $task->upload_date_time != null) {
                $task->updateStatus('uploaded');
            }
        }
    }
}
