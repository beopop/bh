<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manager')->except(['store', 'storeComment']);
        $this->middleware('can:client')->only(['store']);
    }

    public function store(Request $request, Project $project)
    {
        if (Auth::user()->role === 'client' && $project->client_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'nullable|integer',
            'assignee_id' => 'nullable|exists:users,id',
            'due_at' => 'nullable|date',
        ]);

        $task = $project->tasks()->create($data);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'description' => 'Created task '.$task->title,
        ]);

        // Notify admin and manager users about new task
        $recipients = User::whereIn('role', ['admin', 'manager'])->pluck('email');
        if ($recipients->isNotEmpty()) {
            Mail::raw('New task "'.$task->title.'" created by client.', function ($message) use ($recipients) {
                $message->to($recipients->all())->subject('New Task Created');
            });
        }
        return redirect()->route('projects.show', $project);
    }

    public function edit(Project $project, Task $task)
    {
        $comments = $task->comments()->with('user')->orderBy('created_at')->get();
        return view('tasks.edit', compact('project', 'task', 'comments'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,review,done',
            'priority' => 'nullable|integer',
            'assignee_id' => 'nullable|exists:users,id',
            'due_at' => 'nullable|date',
        ]);

        $task->update($data);
        ActivityLog::create([
            'user_id' => Auth::id(),
            'description' => 'Updated task '.$task->title,
        ]);

        // Notify client when task status or details are updated
        $clientEmail = optional($project->client)->contact_email;
        if ($clientEmail) {
            Mail::raw('Task "'.$task->title.'" updated. Current status: '.$task->status.'.', function ($message) use ($clientEmail) {
                $message->to($clientEmail)->subject('Task Updated');
            });
        }
        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project, Task $task)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'description' => 'Deleted task '.$task->title,
        ]);
        $task->delete();
        return redirect()->route('projects.show', $project);
    }

    public function storeComment(Request $request, Project $project, Task $task)
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $task->comments()->create([
            'user_id' => Auth::id(),
            'body' => $data['body'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'description' => 'Commented on task '.$task->title,
        ]);

        // Notify client about new comment if posted by admin/manager
        if (Auth::user()->role !== 'client') {
            $clientEmail = optional($project->client)->contact_email;
            if ($clientEmail) {
                Mail::raw('New comment on task "'.$task->title.'": '.$data['body'], function ($message) use ($clientEmail) {
                    $message->to($clientEmail)->subject('New Task Comment');
                });
            }
        }

        return redirect()->route('projects.tasks.edit', [$project, $task]);
    }
}
