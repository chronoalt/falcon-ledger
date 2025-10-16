<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvssVector extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vector_string',
        'attack_vector',
        'attack_complexity',
        'privileges_required',
        'user_interaction',
        'scope',
        'confidentiality_impact',
        'integrity_impact',
        'availability_impact',
        'base_score',
        'base_severity',
    ];

    public const ATTACK_VECTORS = [
        'network' => ['label' => 'Network', 'abbr' => 'N'],
        'adjacent' => ['label' => 'Adjacent', 'abbr' => 'A'],
        'local' => ['label' => 'Local', 'abbr' => 'L'],
        'physical' => ['label' => 'Physical', 'abbr' => 'P'],
    ];

    public const ATTACK_COMPLEXITIES = [
        'low' => ['label' => 'Low', 'abbr' => 'L'],
        'high' => ['label' => 'High', 'abbr' => 'H'],
    ];

    public const PRIVILEGES_REQUIRED = [
        'none' => ['label' => 'None', 'abbr' => 'N'],
        'low' => ['label' => 'Low', 'abbr' => 'L'],
        'high' => ['label' => 'High', 'abbr' => 'H'],
    ];

    public const USER_INTERACTIONS = [
        'none' => ['label' => 'None', 'abbr' => 'N'],
        'required' => ['label' => 'Required', 'abbr' => 'R'],
    ];

    public const SCOPE_OPTIONS = [
        'unchanged' => ['label' => 'Unchanged', 'abbr' => 'U'],
        'changed' => ['label' => 'Changed', 'abbr' => 'C'],
    ];

    public const IMPACT_METRICS = [
        'none' => ['label' => 'None', 'abbr' => 'N'],
        'low' => ['label' => 'Low', 'abbr' => 'L'],
        'high' => ['label' => 'High', 'abbr' => 'H'],
    ];

    public const SEVERITY_LABELS = [
        'none' => 'None',
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
