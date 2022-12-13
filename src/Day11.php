<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Day11\Monkey;
use Mintopia\Aoc2022\Helpers\Result;

class Day11 extends Day
{
    protected function part1(): Result
    {
        $monkeys = $this->getMonkeys();
        $answer = $this->getMonkeyBusiness($monkeys, 3, 20);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $monkeys = $this->getMonkeys();
        $answer = $this->getMonkeyBusiness($monkeys, 1, 10000);
        return new Result(Result::PART2, $answer);
    }

    protected function getMonkeyBusiness(array $monkeys, int $worryFactor, int $rounds): int
    {
        $maxValue = array_reduce($monkeys, function(int $carry, Monkey $monkey): int {
            return $carry * $monkey->test;
        }, 1);
        for ($i = 0; $i < $rounds; $i++) {
            foreach ($monkeys as $monkey) {
                [$true, $false] = $monkey->inspectAndThrow($worryFactor, $maxValue);
                $monkeys[$monkey->ifTrue]->addItems($true);
                $monkeys[$monkey->ifFalse]->addItems($false);
            }
        }
        usort($monkeys, function($alpha, $bravo) {
            return $bravo->inspections <=> $alpha->inspections;
        });
        return $monkeys[0]->inspections * $monkeys[1]->inspections;
    }

    protected function getMonkeys(): array
    {
        return array_map(function(array $monkey): Monkey {
            return new Monkey($monkey);
        }, array_chunk($this->data, 6));
    }
}