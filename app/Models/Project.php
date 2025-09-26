<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title', 'description', 'status', 'created_by', 'due_at'
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assets() {
        return $this->hasMany(Asset::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
