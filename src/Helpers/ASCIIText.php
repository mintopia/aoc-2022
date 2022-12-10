<?php
namespace Mintopia\Aoc2022\Helpers;

use Symfony\Component\Console\Output\OutputInterface;

class ASCIIText
{
    protected const ON_PIXEL = '<fg=cyan>█</>';
    protected const OFF_PIXEL =' ';

    const FONT = [
        '011010011001111110011001' => 'A',
        '111010011110100110011110' => 'B',
        '011010011000100010010110' => 'C',
        //'' => 'D',
        '111110001110100010001111' => 'E',
        '111110001110100010001000' => 'F',
        '011010011000101110010111' => 'G',
        '100110011111100110011001' => 'H',
        //'' => 'I',
        '001100010001000110010110' => 'J',
        '100110101100101010101001' => 'K',
        '100010001000100010001111' => 'L',
        //'' => 'M',
        //'' => 'N',
        //'' => 'O',
        '111010011001111010001000' => 'P',
        //'' => 'Q',
        '111010011001111010101001' => 'R',
        //'' => 'S',
        //'' => 'T',
        '100110011001100110010110' => 'U',
        //'' => 'V',
        //'' => 'W',
        //'' => 'X',
        //'' => 'Y',
        '111100010010010010001111' => 'Z',
    ];

    public function __construct(protected array $display)
    {

    }

    public function ocr(int $width = 4, int $height = 6, int $space = 1): string
    {
        if (!$this->display) {
            return '';
        }
        $chars = array_fill(0, ceil(strlen($this->display[0]) / ($width + $space)), '');
        $rows = array_fill(0, ceil(count($this->display) / $height), $chars);
        foreach ($this->display as $i => $line) {
            $row = floor($i / $height);
            $chunks = str_split($line, $width + $space);
            foreach ($chunks as $j => $chunk) {
                $chars = substr($chunk, 0, $width);
                $rows[$row][$j] .= $chars;
            }
        }
        $rows = array_map(function(array $row): string {
            return array_reduce($row, function(string $carry, string $chars): string {
                if (array_key_exists($chars, self::FONT)) {
                    return $carry . self::FONT[$chars];
                }
                return $carry . '?';
            }, '');
        }, $rows);
        return implode("\n", $rows);
    }

    public function render(OutputInterface $output): void
    {
        $display = array_map(function(string $line): string {
            return str_replace([0, 1], [self::OFF_PIXEL, self::ON_PIXEL], $line);
        }, $this->display);
        $output->writeln($display);
    }
}