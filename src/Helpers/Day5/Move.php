<?php
namespace Mintopia\Aoc2022\Helpers\Day5;

class Move
{
    public function __construct(public int $number, public int $from, public int $to)
    {

    }

    public function apply(array $stacks, bool $reverse): array
    {
        $from = $stacks[$this->from - 1];
        $to = $stacks[$this->to - 1];

        $slice = array_slice($from, $this->number * -1);
        if ($reverse) {
            $slice = array_reverse($slice);
        }

        $stacks[$this->from - 1] = array_slice($from, 0,count($from) - $this->number);
        $stacks[$this->to - 1] = array_merge($to, $slice);

        return $stacks;
    }
}