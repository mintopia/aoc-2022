<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day9 extends Day
{
    protected const TITLE = 'Rope Bridge';
    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map(function(string $line): array {
            return explode(' ', $line);
        }, $this->data);
    }

    protected function part1(): Result
    {
        $answer = $this->getVisited(2);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->getVisited(10);
        return new Result(Result::PART2, $answer);
    }

    protected function getVisited(int $ropeLength): int
    {
        $visited = [];
        $rope = array_fill(0, $ropeLength, [0, 0]);
        foreach ($this->data as [$direction, $distance]) {
            $index = 0;
            $vector = 1;
            switch ($direction) {
                case 'U':
                    $index = 1;
                    break;
                case 'D':
                    $index = 1;
                    $vector = -1;
                    break;
                case 'L':
                    $vector = -1;
                    break;
            }
            for ($i = 0; $i < $distance; $i++) {
                $rope[0][$index] += $vector;
                for ($j = 1; $j < count($rope); $j++) {
                    $rope[$j] = $this->updateTail($rope[$j - 1], $rope[$j]);
                }
                if (!in_array(end($rope), $visited)) {
                    $visited[] = end($rope);
                }
            }
        }
        return count($visited);
    }

    protected function updateTail($head, $tail): array
    {
        // See if we are adjacent
        $xRange = range($head[0] - 1, $head[0] + 1);
        $yRange = range($head[1] - 1, $head[1] + 1);
        if (in_array($tail[0], $xRange) && in_array($tail[1], $yRange)) {
            return $tail;
        }

        // We are not adjacent, we need to move
        if ($tail[0] < ($head[0])) {
            $tail[0]++;
        }
        if ($tail[0] > ($head[0])) {
            $tail[0]--;
        }
        if ($tail[1] < ($head[1])) {
            $tail[1]++;
        }
        if ($tail[1] > ($head[1])) {
            $tail[1]--;
        }

        return $tail;
    }
}