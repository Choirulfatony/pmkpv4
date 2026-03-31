<?php

namespace App\Libraries;

class Captcha
{
    public function generate(array $config = []): array
    {
        $num1 = random_int($config['min'] ?? 1, $config['max'] ?? 10);
        $num2 = random_int($config['min'] ?? 1, $config['max'] ?? 10);
        
        $operators = ['+', '-'];
        $operator = $operators[array_rand($operators)];
        
        if ($operator === '-') {
            if ($num1 < $num2) {
                [$num1, $num2] = [$num2, $num1];
            }
            $answer = $num1 - $num2;
            $question = "{$num1} - {$num2} = ?";
        } else {
            $answer = $num1 + $num2;
            $question = "{$num1} + {$num2} = ?";
        }

        return [
            'word'     => (string) $answer,
            'question' => $question,
            'html'     => '<span class="fw-bold text-primary" style="font-size: 1.3rem; letter-spacing: 2px;">' . $question . '</span>'
        ];
    }

    public static function validate(string $input, string $sessionWord): bool
    {
        return $input === $sessionWord;
    }
}
