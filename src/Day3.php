<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day3 extends Day
{
    protected const TITLE = 'Rucksack Reorganization';

    protected function part1(): Result
    {
        $score = 0;
        foreach ($this->data as $datum) {
            $half = strlen($datum) / 2;
            $compartment1 = str_split(substr($datum, 0, $half));
            $compartment2 = str_split(substr($datum, $half));
            $inBoth = current(array_intersect($compartment1, $compartment2));
            $score += $this->getPriority($inBoth);

        }
        return new Result(Result::PART1, $score);
    }

    protected function part2(Result $part1): Result
    {
        $score = 0;
        for ($i = 0; $i < count($this->data); $i += 3) {
            $slice = array_slice($this->data, $i, 3);
            $items = array_map(function(string $backpack) {
                return str_split($backpack);
            }, $slice);
            $common = array_intersect($items[0], $items[1], $items[2]);
            $score += $this->getPriority(current($common));
        }
        return new Result(Result::PART2, $score);
    }

    protected function getPriority(string $char): int
    {
        $ord = ord($char);
        if ($ord >= 97) {
            return $ord - 96;
        } else {
            return $ord - 38;
        }
    }
}