<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'status',
        'priority',
        'owner_id',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
