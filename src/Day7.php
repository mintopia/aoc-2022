<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Day7\Directory;
use Mintopia\Aoc2022\Helpers\Day7\File;
use Mintopia\Aoc2022\Helpers\Result;

class Day7 extends Day
{
    protected array $directories;
    protected Directory $root;

    const TOTAL_DISK_SPACE = 70000000;
    const REQUIRED_FREE_SPACE = 30000000;

    protected function loadData(): void
    {
        parent::loadData();
        $this->parseFilesystem();
    }

    protected function parseFilesystem(): void
    {
        $currentDir = null;
        foreach ($this->data as $datum) {
            if ($datum === '$ cd /') {
                $currentDir = new Directory('', null);
                $this->root = $currentDir;
                $this->directories[] = $currentDir;

            } elseif (preg_match('/^(?<size>\d+) (?<name>.*)$/', $datum, $matches)) {
                $file = new File($matches['name'], $currentDir, $matches['size']);
                $currentDir->addFile($file);

            } elseif (preg_match('/^dir (?<name>.*)$/', $datum, $matches)) {
                $dir = new Directory($matches['name'], $currentDir);
                $currentDir->addDir($dir);
                $this->directories[] = $dir;

            } elseif (preg_match('/^\$ cd (?<name>.*)$/', $datum, $matches)) {
                $name = $matches['name'];
                if ($name === '..') {
                    // Up a directory
                    if (!$currentDir->parent) {
                        throw new \Exception("At root level, can't go up a directory");
                    }
                    $currentDir = $currentDir->parent;
                } else {
                    // Enter a directory
                    if (!array_key_exists($name, $currentDir->dirs)) {
                        throw new \Exception("Directory does not exist: {$datum}");
                    }
                    $currentDir = $currentDir->dirs[$name];
                }
            }
        }
    }

    protected function part1(): Result
    {
        $answer = 0;
        foreach ($this->directories as $dir) {
            $size = $dir->getSize();
            if ($size <= 100000) {
                $answer += $size;
            }
        }
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $needToDelete = self::REQUIRED_FREE_SPACE - self::TOTAL_DISK_SPACE + $this->root->getSize();
        $answer = PHP_INT_MAX;
        foreach ($this->directories as $dir) {
            $size = $dir->getSize();
            if ($size >= $needToDelete) {
                $answer = min($answer, $size);
            }
        }
        return new Result(Result::PART2, $answer);
    }
}