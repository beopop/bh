<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manager');
    }

    public function index(Request $request)
    {
        $query = Client::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('contact_email', 'like', "%$search%")
                  ->orWhere('contact_phone', 'like', "%$search%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($tags = $request->get('tags')) {
            $tagsArr = array_filter(array_map('trim', explode(',', $tags)));
            foreach ($tagsArr as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $clients = $query->paginate(15);

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:lead,active,paused,archived',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $data['tags'] = $this->parseTags($data['tags'] ?? '');

        $client = Client::create($data);

        return redirect()->route('clients.show', $client);
    }

    public function show(Client $client)
    {
        $client->load('activities');
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:lead,active,paused,archived',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $data['tags'] = $this->parseTags($data['tags'] ?? '');

        $client->update($data);

        return redirect()->route('clients.show', $client);
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index');
    }

    public function storeActivity(Request $request, Client $client)
    {
        $data = $request->validate([
            'description' => 'required|string',
        ]);

        $client->activities()->create($data);

        return redirect()->route('clients.show', $client);
    }

    public function export(Client $client)
    {
        $client->load('projects.tasks');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="client_'.$client->id.'_tasks.csv"',
        ];
        $callback = function () use ($client) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Project', 'Task', 'Status', 'Priority', 'Due Date']);
            foreach ($client->projects as $project) {
                foreach ($project->tasks as $task) {
                    fputcsv($handle, [
                        $project->name,
                        $task->title,
                        $task->status,
                        $task->priority,
                        optional($task->due_at)->toDateString(),
                    ]);
                }
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    private function parseTags(string $tags): array
    {
        return array_filter(array_map('trim', explode(',', $tags)));
    }
}
