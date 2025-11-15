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

    public static function findOrCreateFromMetrics(array $metrics): self
    {
        $query = self::query();
        foreach ($metrics as $key => $value) {
            $query->where($key, $value);
        }
        $vector = $query->first();

        if ($vector) {
            return $vector;
        }

        // --- Calculation logic from Seeder ---
        $attackVectors = [
            'network' => ['abbr' => 'N', 'weight' => 0.85],
            'adjacent' => ['abbr' => 'A', 'weight' => 0.62],
            'local' => ['abbr' => 'L', 'weight' => 0.55],
            'physical' => ['abbr' => 'P', 'weight' => 0.20],
        ];
        $attackComplexities = [
            'low' => ['abbr' => 'L', 'weight' => 0.77],
            'high' => ['abbr' => 'H', 'weight' => 0.44],
        ];
        $privilegesRequired = [
            'none' => ['abbr' => 'N', 'weight' => ['unchanged' => 0.85, 'changed' => 0.85]],
            'low' => ['abbr' => 'L', 'weight' => ['unchanged' => 0.62, 'changed' => 0.68]],
            'high' => ['abbr' => 'H', 'weight' => ['unchanged' => 0.27, 'changed' => 0.50]],
        ];
        $userInteractions = [
            'none' => ['abbr' => 'N', 'weight' => 0.85],
            'required' => ['abbr' => 'R', 'weight' => 0.62],
        ];
        $impactMetricsWeights = [
            'none' => 0.00,
            'low' => 0.22,
            'high' => 0.56,
        ];
        // ---

        $scopeKey = $metrics['scope'];

        $baseScore = self::calculateBaseScore(
            $scopeKey,
            $attackVectors[$metrics['attack_vector']]['weight'],
            $attackComplexities[$metrics['attack_complexity']]['weight'],
            $privilegesRequired[$metrics['privileges_required']]['weight'][$scopeKey],
            $userInteractions[$metrics['user_interaction']]['weight'],
            $impactMetricsWeights[$metrics['confidentiality_impact']],
            $impactMetricsWeights[$metrics['integrity_impact']],
            $impactMetricsWeights[$metrics['availability_impact']]
        );

        $severity = self::mapSeverity($baseScore);

        $vectorString = sprintf(
            'CVSS:3.1/AV:%s/AC:%s/PR:%s/UI:%s/S:%s/C:%s/I:%s/A:%s',
            self::ATTACK_VECTORS[$metrics['attack_vector']]['abbr'],
            self::ATTACK_COMPLEXITIES[$metrics['attack_complexity']]['abbr'],
            self::PRIVILEGES_REQUIRED[$metrics['privileges_required']]['abbr'],
            self::USER_INTERACTIONS[$metrics['user_interaction']]['abbr'],
            self::SCOPE_OPTIONS[$metrics['scope']]['abbr'],
            self::IMPACT_METRICS[$metrics['confidentiality_impact']]['abbr'],
            self::IMPACT_METRICS[$metrics['integrity_impact']]['abbr'],
            self::IMPACT_METRICS[$metrics['availability_impact']]['abbr']
        );

        return self::create(array_merge($metrics, [
            'vector_string' => $vectorString,
            'base_score' => number_format($baseScore, 1, '.', ''),
            'base_severity' => $severity,
        ]));
    }

    private static function calculateBaseScore(
        string $scope,
        float $attackVectorWeight,
        float $attackComplexityWeight,
        float $privilegesRequiredWeight,
        float $userInteractionWeight,
        float $confidentialityWeight,
        float $integrityWeight,
        float $availabilityWeight
    ): float {
        $iscBase = 1 - ((1 - $confidentialityWeight) * (1 - $integrityWeight) * (1 - $availabilityWeight));

        if ($iscBase <= 0) {
            return 0.0;
        }

        $impact = $scope === 'unchanged'
            ? 6.42 * $iscBase
            : 7.52 * ($iscBase - 0.029) - 3.25 * pow(($iscBase - 0.02), 15);

        $exploitability = 8.22 * $attackVectorWeight * $attackComplexityWeight * $privilegesRequiredWeight * $userInteractionWeight;

        if ($impact <= 0) {
            return 0.0;
        }

        $score = $scope === 'unchanged'
            ? min($impact + $exploitability, 10)
            : min(1.08 * ($impact + $exploitability), 10);

        return self::roundUp($score);
    }

    private static function roundUp(float $score): float
    {
        return ceil($score * 10) / 10;
    }

    private static function mapSeverity(float $score): string
    {
        if ($score == 0.0) {
            return 'none';
        }
        if ($score <= 3.9) {
            return 'low';
        }
        if ($score <= 6.9) {
            return 'medium';
        }
        if ($score <= 8.9) {
            return 'high';
        }

        return 'critical';
    }
}
