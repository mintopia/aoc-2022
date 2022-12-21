<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day7 extends Day
{
    protected const TITLE = 'No Space Left On Device';
    protected array $sizes = [];

    const TOTAL_DISK_SPACE = 70000000;
    const REQUIRED_FREE_SPACE = 30000000;
    const MAXIMUM_SIZE = 100000;

    protected function loadData(): void
    {
        parent::loadData();

        $currentPath = [];
        foreach ($this->data as $datum) {
            if (preg_match('/^(?<size>\d+) (?<name>.*)$/', $datum, $matches)) {
                foreach ($currentPath as $i => $path) {
                    $key = implode('/', array_slice($currentPath, 0, $i + 1));
                    $this->sizes[$key] += $matches['size'];
                }

            } elseif (preg_match('/^\$ cd (?<name>.*)$/', $datum, $matches)) {
                if ($matches['name'] === '..') {
                    array_pop($currentPath);
                } else {
                    $currentPath[] = $matches['name'];
                    $key = implode('/', $currentPath);
                    $this->sizes[$key] = 0;
                }
            }
        }
    }

    protected function part1(): Result
    {
        $answer = 0;
        foreach ($this->sizes as $size) {
            if ($size <= self::MAXIMUM_SIZE) {
                $answer += $size;
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $needToDelete = self::REQUIRED_FREE_SPACE - self::TOTAL_DISK_SPACE + current($this->sizes);
        $answer = PHP_INT_MAX;
        foreach ($this->sizes as $size) {
            if ($size >= $needToDelete) {
                $answer = min($answer, $size);
            }
        }
        return new Result(Result::PART2, $answer);
    }
}