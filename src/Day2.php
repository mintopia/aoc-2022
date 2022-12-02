<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day2 extends Day
{
    const PART1_LOOKUP = [
        'A X' => 4,
        'A Y' => 8,
        'A Z' => 3,
        'B X' => 1,
        'B Y' => 5,
        'B Z' => 9,
        'C X' => 7,
        'C Y' => 2,
        'C Z' => 6,
    ];
    const PART2_LOOKUP = [
        'A X' => 3,
        'A Y' => 4,
        'A Z' => 8,
        'B X' => 1,
        'B Y' => 5,
        'B Z' => 9,
        'C X' => 2,
        'C Y' => 6,
        'C Z' => 7,
    ];

    protected function part1(): Result
    {
        $score = 0;
        foreach ($this->data as $datum) {
            $score += self::PART1_LOOKUP[$datum];
        }
        return new Result(Result::PART1, $score);
    }

    protected function part2(Result $part1): Result
    {
        $score = 0;
        foreach ($this->data as $datum) {
            $score += self::PART2_LOOKUP[$datum];

        }
        return new Result(Result::PART2, $score);
    }
}