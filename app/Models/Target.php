<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'asset_id',
        'label',
        'endpoint',
        'description',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
