<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FindingAttachment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'finding_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function finding()
    {
        return $this->belongsTo(Finding::class);
    }
}
