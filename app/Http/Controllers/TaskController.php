<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return redirect()->route('projects.tasks.edit', [$project, $task]);
    }
}
