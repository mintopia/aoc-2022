<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day1 extends Day
{
    protected function part1(): Result
    {
        $output = implode(', ', $this->data);
        return new Result(Result::PART1, $output);
    }

    protected function part2(Result $part1): Result
    {
        $output = implode(', ', $this->data) . '!';
        return new Result(Result::PART2, $output);
    }
}