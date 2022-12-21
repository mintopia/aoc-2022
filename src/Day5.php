<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Day5\Move;
use Mintopia\Aoc2022\Helpers\Result;

class Day5 extends Day
{
    protected const TITLE = 'Supply Stacks';
    protected $stacks = [];
    protected $moves = [];

    protected function loadData(): void
    {
        $lines = explode("\n", $this->getInputFile());
        foreach ($lines as $line) {
            $matches = [];
            if (preg_match('/^move (?<number>\d+) from (?<from>\d+) to (?<to>\d+)$/', $line, $matches)) {
                $this->moves[] = new Move($matches['number'], $matches['from'], $matches['to']);
            } elseif (strpos($line, '[') !== false) {
                for ($i = 0; $i < strlen($line); $i += 4) {
                    $char = substr($line, $i + 1, 1);
                    if ($char === " ") {
                        continue;
                    }
                    if (!isset($this->stacks[$i / 4])) {
                        $this->stacks[$i / 4] = [];
                    }
                    array_unshift($this->stacks[$i / 4], $char);
                }
            }
        }
        ksort($this->stacks);
    }

    protected function part1(): Result
    {
        $answer = $this->applyMoves(true);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->applyMoves(false);
        return new Result(Result::PART2, $answer);
    }

    protected function applyMoves(bool $reverse): string
    {
        $stacks = $this->stacks;
        foreach ($this->moves as $move) {
            $stacks = $move->apply($stacks, $reverse);
        }
        return array_reduce($stacks, function(string $carry, array $stack) {
            return $carry . end($stack);
        }, '');
    }
}