<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day20 extends Day
{
    protected const TITLE = 'Grove Positioning System';
    protected const DECRYPTION_KEY = 811589153;

    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map(function(int $index, string $datum): object {
            return (object) [
                'value' => (int) $datum,
                'index' => $index,
            ];
        }, array_keys($this->data), $this->data);
    }

    protected function part1(): Result
    {
        $data = $this->data;
        $answer = $this->getAnswer($data, 1);
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $data = array_map(function(object $data): object {
            $clone = clone($data);
            $clone->value *= self::DECRYPTION_KEY;
            return $clone;
        }, $this->data);
        $answer = $this->getAnswer($data, 10);
        return new Result(Result::PART2, $answer);
    }

    protected function getAnswer(array $data, int $mix): int
    {
        $original = $data;
        for ($i = 0; $i < $mix; $i++) {
            $data = $this->mix($original, $data);
        }
        $zeroIndex = null;
        foreach ($data as $zeroIndex => $obj) {
            if ($obj->value === 0) {
                break;
            }
        }
        if ($zeroIndex === null) {
            throw new \Exception('Unable to find index of 0');
        }

        $count = count($data);
        $answer = 0;
        foreach ([1000, 2000, 3000] as $offset) {
            $answer += $data[($zeroIndex + $offset) % $count]->value;
        }
        return $answer;
    }

    protected function mix(array $originalData, array $input): array
    {
        $count = count($input);
        foreach ($originalData as $original) {
            $index = array_search($original, $input);
            $data = $input[$index];
            $newIndex = ($index + $data->value) % ($count - 1);
            unset($input[$index]);
            array_splice($input, $newIndex, 0, [$data]);
        }
        return $input;
    }
}