<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function create()
    {
        return view('files.upload');
    }

    public function store(Request $request)
    {
        $maxKb = config('app.file_max_size_mb', 5) * 1024;

        $request->validate([
            'file' => ['required', 'file', 'max:' . $maxKb],
            'owner_type' => ['nullable', 'string'],
            'owner_id' => ['nullable', 'integer'],
        ]);

        $uploaded = $request->file('file');
        $storedName = Str::uuid()->toString();
        if ($extension = $uploaded->getClientOriginalExtension()) {
            $storedName .= '.' . $extension;
        }
        $uploaded->storeAs('uploads', $storedName);

        $file = File::create([
            'owner_type' => $request->input('owner_type', Auth::user()->getMorphClass()),
            'owner_id' => $request->input('owner_id', Auth::id()),
            'original_name' => $uploaded->getClientOriginalName(),
            'stored_name' => $storedName,
            'mime' => $uploaded->getClientMimeType(),
            'size' => $uploaded->getSize(),
            'hash' => hash_file('sha256', $uploaded->getRealPath()),
            'uploader_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return response()->json(['id' => $file->id]);
    }

    public function download(Request $request, File $file)
    {
        if (!$request->hasValidSignature()) {
            abort(403);
        }

        return Storage::download('uploads/' . $file->stored_name, $file->original_name);
    }
}
