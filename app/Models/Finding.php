<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finding extends Model
{
    use HasFactory, HasUuids;

    public const STATUS_OPTIONS = [
        'open',
        'in_progress',
        'mitigated',
        'closed',
    ];

    protected $fillable = [
        'target_id',
        'cvss_vector_id',
        'title',
        'status',
        'description',
        'recommendation',
    ];

    public function cvssVector()
    {
        return $this->belongsTo(CvssVector::class);
    }

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function attachments()
    {
        return $this->hasMany(FindingAttachment::class);
    }
}
