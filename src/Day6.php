<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day6 extends Day
{
    protected const TITLE = 'Tuning Trouble';
    public function loadData(): void
    {
        parent::loadData();
        $this->data = str_split($this->data[0]);
    }

    protected function part1(): Result
    {
        $answer = $this->getUniqueSegment(4);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->getUniqueSegment(14);
        return new Result(Result::PART2, $answer);
    }

    protected function getUniqueSegment(int $size): int
    {
        for ($i = 0; $i < count($this->data) - $size; $i++) {
            $slice = array_slice($this->data, $i, $size);
            $unique = array_unique($slice);
            if (count($unique) === $size) {
                return $i + $size;
            }
        }
        throw new \Exception('Unable to find unique segment');
    }
}