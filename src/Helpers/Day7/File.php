<?php
namespace Mintopia\Aoc2022\Helpers\Day7;

class File
{
    public function __construct(public string $name, public Directory $parent, public int $size)
    {

    }
}