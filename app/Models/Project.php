<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'status', 'created_by', 'due_at'
    ];

    public function owner() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assets() {
        return $this->hasMany(Asset::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }
}
