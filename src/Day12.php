<?php
namespace Mintopia\Aoc2022;

use Mintopia\Aoc2022\Helpers\Result;

class Day12 extends Day
{
    protected bool $hasVisualisation = true;
    protected function loadData(): void
    {
        parent::loadData();
        $this->data = array_map('str_split', $this->data);
    }

    protected function part1(): Result
    {
        $start = $this->find('S');
        [$visited, $path] = $this->getShortestPath($start);
        if ($this->input->getOption('visualise')) {
            $this->renderPath($path[0], end($path), end($path), $path, $visited);
        }
        $answer = count($path) - 1;
        return new Result(Result::PART1, $answer);
    }

    protected function part2(Result $part1): Result
    {
        $start = $this->find('E');

        [$visited, $path] = $this->getShortestPath($start, true);
        if ($this->input->getOption('visualise')) {
            $this->renderPath(end($path), $start, $start, $path, $visited);
        }
        $answer = count($path) - 1;
        return new Result(Result::PART2, $answer);
    }

    protected function getStartingPoints(array $start): array
    {
        $height = count($this->data);
        $width = count($this->data[0]);

        $points = [$start];

        $queue = new \SplQueue();
        $queue->enqueue($start);
        while (!$queue->isEmpty()) {
            [$row, $column] = $queue->dequeue();
            $neighbours = [
                [$row, $column - 1],
                [$row, $column + 1],
                [$row - 1, $column],
                [$row + 1, $column],
            ];
            foreach ($neighbours as [$nRow, $nColumn]) {
                if (in_array([$nRow, $nColumn], $points)) {
                    continue;
                }
                if ($nRow < 0 || $nRow >= $height) {
                    continue;
                }
                if ($nColumn < 0 || $nColumn >= $width) {
                    continue;
                }
                $nHeight = $this->getHeight($nRow, $nColumn);
                if ($nHeight != 1) {
                    continue;
                }

                $points[] = [$nRow, $nColumn];
                $queue->enqueue([$nRow, $nColumn]);
            }
        }

        return $points;
    }

    protected function getShortestPath(array $start, bool $reverse = false): array
    {
        $height = count($this->data);
        $width = count($this->data[0]);

        $end = $this->find('E');

        $queue = new \SplQueue();
        $queue->enqueue([$start]);

        $visited = [$start];

        while (!$queue->isEmpty()) {
            $path = $queue->dequeue();
            [$row, $column] = end($path);
            $currentHeight = $this->getHeight($row, $column);

            $neighbours = [
                [$row, $column - 1],
                [$row, $column + 1],
                [$row - 1, $column],
                [$row + 1, $column],
            ];
            foreach ($neighbours as [$nRow, $nColumn]) {
                if (in_array([$nRow, $nColumn], $visited)) {
                    continue;
                }
                if ($nRow < 0 || $nRow >= $height) {
                    continue;
                }
                if ($nColumn < 0 || $nColumn >= $width) {
                    continue;
                }

                $nHeight = $this->getHeight($nRow, $nColumn);
                if ($reverse && $nHeight < ($currentHeight - 1)) {
                    continue;
                }
                if (!$reverse && $nHeight > ($currentHeight + 1)) {
                    continue;
                }

                $visited[] = [$nRow, $nColumn];
                $newPath = $path;
                $newPath[] = [$nRow, $nColumn];

                if (!$reverse && ([$nRow, $nColumn] == $end)) {
                    return [$visited, $newPath];
                } elseif ($reverse && $nHeight === 1) {
                    return [$visited, $newPath];
                } else {
                    $queue->enqueue($newPath);
                }
            }
        }
        return [$visited, []];
    }

    protected function getHeight(int $row, int $column): int
    {
        $height = $this->data[$row][$column];
        if ($height === 'S') {
            return 1;
        } elseif ($height === 'E') {
            return 26;
        }
        return ord($height) - 96;
    }

    protected function find($needle): array
    {
        foreach ($this->data as $rowNumber => $row) {
            $index = array_search($needle, $row);
            if ($index !== false) {
                return [$rowNumber, $index];
            }
        }
        throw new \Exception("Unable to find {$needle}");
    }

    protected function renderPath(array $start, array $head, array $end, array $path, array $visited): array
    {

        foreach ($this->data as $row => $rowValues) {
            $line = '';
            foreach ($rowValues as $column => $height) {
                $height = chr($this->getHeight($row, $column) + 96);
                if ([$row, $column] == $start) {
                   $line .='<fg=green>S</>';
                } elseif ([$row, $column] == $end) {
                    $line .='<fg=red>E</>';
                } elseif ([$row, $column] == $head) {
                    $line .= "<fg=cyan>{$height}</>";
                } elseif (in_array([$row, $column], $path)) {
                    $line .= "<fg=yellow>{$height}</>";
                } elseif (in_array([$row, $column], $visited)) {
                    $line .= "{$height}";
                } else {
                    $line .= "<fg=gray>{$height}</>";
                }
            }
            $this->output->writeln($line);
        }
        return $path;
    }

}