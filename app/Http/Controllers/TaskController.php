<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manager')->except(['store']);
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

        $project->tasks()->create($data);
        return redirect()->route('projects.show', $project);
    }

    public function edit(Project $project, Task $task)
    {
        return view('tasks.edit', compact('project', 'task'));
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
        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();
        return redirect()->route('projects.show', $project);
    }
}
