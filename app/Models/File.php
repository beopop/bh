<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;

class File extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'original_name',
        'stored_name',
        'mime',
        'size',
        'hash',
        'uploader_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function temporaryUrl(): string
    {
        return URL::temporarySignedRoute('files.download', now()->addMinutes(5), ['file' => $this->id]);
    }
}
