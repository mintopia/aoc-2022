<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day1 extends Day
{
    protected const TITLE = 'Calorie Counting';

    protected function loadData(): void
    {
        $this->data = $this->getInputFile();
    }

    protected function part1(): Result
    {
        $totals = [];
        $elves = explode("\n\n", $this->data);
        foreach ($elves as $elf) {
            $totals[] = array_sum(explode("\n", $elf));
        }
        sort($totals);
        return new Result(Result::PART1, end($totals), $totals);
    }

    protected function part2(Result $part1): Result
    {
        $top3 = array_slice($part1->carry, -3);
        $total = array_sum($top3);
        return new Result(Result::PART2, $total);
    }
}