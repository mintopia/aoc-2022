<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day21 extends Day
{
    protected array $unsolved = [];
    protected array $solved = [];

    protected bool $hasVisualisation = true;

    protected function loadData(): void
    {
        parent::loadData();
        foreach ($this->data as $datum) {
            [$name, $value] = explode(': ', $datum);
            if (is_numeric($value)) {
                $this->solved[$name] = (int) $value;
            } else {
                $this->unsolved[$name] = explode(' ', $value);
            }
        }
    }

    protected function part1(): Result
    {
        $answer = $this->resolve($this->solved, $this->unsolved, 'root');
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $answer = $this->resolveReverse($this->solved, $this->unsolved, 'humn');
        return new Result(Result::PART2, $answer);
    }

    protected function resolve(array $solved, array $unsolved, string $name): int
    {
        if (isset($solved[$name])) {
            return $solved[$name];
        }

        [$monkey1, $operator, $monkey2] = $unsolved[$name];

        $left = $this->resolve($solved, $unsolved, $monkey1);
        $right = $this->resolve($solved, $unsolved, $monkey2);

        $result = match($operator) {
            '+' => $left + $right,
            '-' => $left - $right,
            '*' => $left * $right,
            '/' => $left / $right
        };

        if ($this->input->getOption('visualise')) {
            $this->output->writeln("{$name}: {$left} {$operator} {$right} = {$result}");
        }

        return $result;
    }

    protected function resolveReverse(array $solved, array $unsolved, string $name) {
        foreach ($unsolved as $currentMonkey => [$monkey1, $operator, $monkey2]) {
            if ($monkey1 === $name) {
                // Left hand side is our target, so get our right hand side value
                $right = $this->resolve($solved, $unsolved, $monkey2);
                if ($currentMonkey === 'root') {
                    return $right;
                }

                // Now get our result of the operation
                $result = $this->resolveReverse($solved, $unsolved, $currentMonkey);

                // We are trying to find the left hand side of the operation, so perform the inverse
                $left = match ($operator) {
                    '+' => $result - $right,
                    '-' => $result + $right,
                    '*' => $result / $right,
                    '/' => $result * $right
                };

                if ($this->input->getOption('visualise')) {
                    $this->output->writeln("{$currentMonkey}: <fg=yellow>{$left}</> {$operator} {$right} = {$result}");
                }

                return $left;

            } elseif ($monkey2 === $name) {
                // Our target is the right hand side of the operation
                $left = $this->resolve($solved, $unsolved, $monkey1);

                if ($currentMonkey === 'root') {
                    return $left;
                } else {
                    // Get the result of the operation
                    $result = $this->resolveReverse($solved, $unsolved, $currentMonkey);

                    // We are the right hand side of the operation, so we need to invert the operations and invert the non-commutative operators
                    $right = match ($operator) {
                        '+' => $result - $left,
                        '-' => $left - $result,
                        '*' => $result / $left,
                        '/' => $left / $result
                    };

                    if ($this->input->getOption('visualise')) {
                        $this->output->writeln("{$currentMonkey}: {$left} {$operator} <fg=yellow>{$right}</> = {$result}");
                    }

                    return $right;
                }
            }
        }
        return 0;
    }
}