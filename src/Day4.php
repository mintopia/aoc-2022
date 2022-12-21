<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day4 extends Day
{
    protected const TITLE = 'Camp Cleanup';

    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map(function(string $line) {
            return $this->getSections($line);
        }, $this->data);
    }

    protected function part1(): Result
    {
        $score = 0;
        foreach ($this->data as [$section1, $section2]) {
            if (array_diff($section1, $section2) === [] || array_diff($section2, $section1) === []) {
                $score++;
            }
        }
        return new Result(Result::PART1, $score);
    }

    protected function part2(Result $part1): Result
    {
        $score = 0;
        foreach ($this->data as [$section1, $section2]) {
            if (array_intersect($section1, $section2) !== [] || array_intersect($section2, $section1) !== []) {
                $score++;
            }
        }
        return new Result(Result::PART2, $score);
    }

    protected function getSections(string $input): array
    {
        $sections = explode(',', $input);
        return array_map(function(string $section) {
            [$start, $end] = explode('-', $section);
            return range($start, $end);
        }, $sections);
    }
}