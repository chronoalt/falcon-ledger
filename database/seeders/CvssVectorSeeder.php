<?php

namespace Database\Seeders;

use App\Models\CvssVector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CvssVectorSeeder extends Seeder
{
    public function run(): void
    {
        if (CvssVector::query()->exists()) {
            return;
        }

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

        $scopeOptions = [
            'unchanged' => ['abbr' => 'U'],
            'changed' => ['abbr' => 'C'],
        ];

        $impactMetrics = [
            'none' => ['abbr' => 'N', 'weight' => 0.00],
            'low' => ['abbr' => 'L', 'weight' => 0.22],
            'high' => ['abbr' => 'H', 'weight' => 0.56],
        ];

        $rows = [];
        $now = now();

        foreach ($scopeOptions as $scopeKey => $scopeMeta) {
            foreach ($attackVectors as $avKey => $avMeta) {
                foreach ($attackComplexities as $acKey => $acMeta) {
                    foreach ($privilegesRequired as $prKey => $prMeta) {
                        foreach ($userInteractions as $uiKey => $uiMeta) {
                            foreach ($impactMetrics as $cKey => $cMeta) {
                                foreach ($impactMetrics as $iKey => $iMeta) {
                                    foreach ($impactMetrics as $aKey => $aMeta) {
                                        $metrics = [
                                            'attack_vector' => $avKey,
                                            'attack_complexity' => $acKey,
                                            'privileges_required' => $prKey,
                                            'user_interaction' => $uiKey,
                                            'scope' => $scopeKey,
                                            'confidentiality_impact' => $cKey,
                                            'integrity_impact' => $iKey,
                                            'availability_impact' => $aKey,
                                        ];

                                        $baseScore = $this->calculateBaseScore(
                                            $scopeKey,
                                            $avMeta['weight'],
                                            $acMeta['weight'],
                                            $privilegesRequired[$prKey]['weight'][$scopeKey],
                                            $uiMeta['weight'],
                                            $cMeta['weight'],
                                            $iMeta['weight'],
                                            $aMeta['weight']
                                        );

                                        $severity = $this->mapSeverity($baseScore);

                                        $vectorString = sprintf(
                                            'CVSS:3.1/AV:%s/AC:%s/PR:%s/UI:%s/S:%s/C:%s/I:%s/A:%s',
                                            $avMeta['abbr'],
                                            $acMeta['abbr'],
                                            $prMeta['abbr'],
                                            $uiMeta['abbr'],
                                            $scopeMeta['abbr'],
                                            $cMeta['abbr'],
                                            $iMeta['abbr'],
                                            $aMeta['abbr']
                                        );

                                        $rows[] = [
                                            'id' => (string) Str::uuid(),
                                            'vector_string' => $vectorString,
                                            'attack_vector' => $metrics['attack_vector'],
                                            'attack_complexity' => $metrics['attack_complexity'],
                                            'privileges_required' => $metrics['privileges_required'],
                                            'user_interaction' => $metrics['user_interaction'],
                                            'scope' => $metrics['scope'],
                                            'confidentiality_impact' => $metrics['confidentiality_impact'],
                                            'integrity_impact' => $metrics['integrity_impact'],
                                            'availability_impact' => $metrics['availability_impact'],
                                            'base_score' => number_format($baseScore, 1, '.', ''),
                                            'base_severity' => $severity,
                                            'created_at' => $now,
                                            'updated_at' => $now,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('cvss_vectors')->insert($chunk);
        }
    }

    private function calculateBaseScore(
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

        return $this->roundUp($score);
    }

    private function roundUp(float $score): float
    {
        return ceil($score * 10) / 10;
    }

    private function mapSeverity(float $score): string
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
