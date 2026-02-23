<?php

namespace App\Services;

class SpacedRepetitionService
{
    const RATINGS = [
        'again' => 0,
        'hard'  => 2,
        'good'  => 4,
        'easy'  => 5,
    ];

    public function calculate(array $progress, string $rating): array
    {
        $q  = self::RATINGS[$rating];
        $ef = $progress['easiness_factor'];
        $n  = $progress['repetitions'];
        $i  = $progress['interval'];

        if ($q < 3) {
            $n = 0;
            $i = 1;
        } else {
            if ($n === 0)      $i = 1;
            elseif ($n === 1)  $i = 6;
            else               $i = round($i * $ef);
            $n++;
        }

        $ef = $ef + (0.1 - (5 - $q) * (0.08 + (5 - $q) * 0.02));
        $ef = max(1.3, round($ef, 2));

        return [
            'repetitions'     => $n,
            'interval'        => $i,
            'easiness_factor' => $ef,
            'next_review_at'  => now()->addDays($i),
        ];
    }
}
