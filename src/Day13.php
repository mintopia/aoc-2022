<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day13 extends Day
{
    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map('json_decode', $this->data);
    }

    protected function part1(): Result
    {
        $answer = 0;
        for ($i = 0; $i < count($this->data); $i += 2) {
            $result = $this->shouldSwap($this->data[$i], $this->data[$i + 1]);
            if ($result === -1) {
                $answer += ($i / 2) + 1;
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $data = $this->data;
        $data[] = [[2]];
        $data[] = [[6]];
        usort($data, [$this, 'shouldSwap']);

        $index1 = array_search([[2]], $data) + 1;
        $index2 = array_search([[6]], $data) + 1;

        $answer = $index1 * $index2;
        return new Result(Result::PART2, $answer);
    }

    protected function shouldSwap($left, $right): int
    {
        if (is_integer($left) && is_integer($right)) {
            return $left <=> $right;
        }

        if ($left !== null && $right === null) {
            return 1;
        } elseif ($right !== null && $left === null) {
            return -1;
        }

        if (is_integer($left)) {
            $left = [$left];
        } elseif (is_integer($right)) {
            $right = [$right];
        }

        $max = max(count($left), count($right));
        for ($i = 0; $i < $max; $i++) {
            $leftChild = $left[$i] ?? null;
            $rightChild = $right[$i] ?? null;
            $result = $this->shouldSwap($leftChild, $rightChild);
            if ($result !== 0) {
                return $result;
            }
        }
        return 0;
    }
}