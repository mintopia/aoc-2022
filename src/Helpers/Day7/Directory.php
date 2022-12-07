<?php
namespace Mintopia\Aoc2022\Helpers\Day7;

class Directory
{
    public $files = [];
    public $dirs = [];

    protected ?int $size = null;

    public function __construct(public string $name, public ?Directory $parent = null)
    {

    }

    public function getSize(): int
    {
        if ($this->size !== null) {
            return $this->size;
        }

        $this->size = array_reduce($this->files, function(int $size, File $file) {
            return $size + $file->size;
        }, 0);
        $this->size = array_reduce($this->dirs, function(int $size, Directory $directory) {
            return $size + $directory->getSize();
        }, $this->size);
        return $this->size;
    }

    public function addFile(File $file): Directory
    {
        $this->files[$file->name] = $file;
        return $this;
    }

    public function addDir(Directory $dir): Directory
    {
        $this->dirs[$dir->name] = $dir;
        return $this;
    }
}