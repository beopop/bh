<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manager')->except(['index', 'show']);
        $this->middleware('can:client')->only(['index', 'show']);
    }

    public function index()
    {
        $query = Project::query();
        if (Auth::user()->role === 'client') {
            $query->where('client_id', Auth::id());
        }
        $projects = $query->paginate(15);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,on-hold,completed,archived',
            'priority' => 'nullable|integer',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $project = Project::create($data);
        return redirect()->route('projects.show', $project);
    }

    public function show(Project $project)
    {
        if (Auth::user()->role === 'client' && $project->client_id !== Auth::id()) {
            abort(403);
        }
        $tasks = $project->tasks()->orderBy('priority')->get()->groupBy('status');
        return view('projects.show', compact('project', 'tasks'));
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:planned,active,on-hold,completed,archived',
            'priority' => 'nullable|integer',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $project->update($data);
        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index');
    }

    public function export(Project $project)
    {
        $project->load('tasks');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="project_'.$project->id.'_tasks.csv"',
        ];
        $callback = function () use ($project) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Task', 'Status', 'Priority', 'Due Date']);
            foreach ($project->tasks as $task) {
                fputcsv($handle, [
                    $task->title,
                    $task->status,
                    $task->priority,
                    optional($task->due_at)->toDateString(),
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
}
