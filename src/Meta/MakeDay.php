<?php
namespace Mintopia\Aoc2022\Meta;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeDay extends Command
{
    protected static $defaultName = 'make:day';

    protected const FILE_MAPPING = [
        'Skeleton/Day.php.skeleton' => 'src/Day{:day}.php',
        'Skeleton/input.txt' => 'input/day{:day}.txt',
        'Skeleton/test-input.txt' => 'testdata/input/day{:day}.txt',
        'Skeleton/test-output.txt' => 'testdata/output/day{:day}.txt',
    ];

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $io;

    protected function configure(): void
    {
        $this->setDescription("Add a new day");
        $this->addArgument('number', InputArgument::REQUIRED, 'The day number to create');
        $this->addOption('fetch', 'f', InputOption::VALUE_NONE, 'Fetch the puzzle input for this day');
        $this->addOption('year', 'y', InputOption::VALUE_OPTIONAL, 'The year to fetch input for');
        $this->addOption('insecure', 'i', InputOption::VALUE_NONE, "Don't verify TLS certificates");
        $this->addOption('cookie', 'c', InputOption::VALUE_OPTIONAL, 'Your adventofcode.com session cookie value');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($this->input, $this->output);

        $this->io->title('Advent of Code Day Skeleton Creator');

        if (!$day = $this->getDay()) {
            return self::FAILURE;
        }

        $mapping = $this->getMapping($day);
        if (!$this->checkExistingFiles($mapping)) {
            return self::FAILURE;
        }

        if (!$this->copyFiles($mapping, $day)) {
            return self::FAILURE;
        }

        if ($this->input->getOption('fetch')) {
            $result = $this->fetchPuzzleInput($day);
            if ($result !== self::SUCCESS) {
                return $result;
            }
        }

        return self::SUCCESS;
    }

    protected function fetchPuzzleInput(int $day): int
    {
        $command = $this->getApplication()->find('make:input');
        $args = [
            'day' => $day,
        ];
        if ($this->input->getOption('insecure')) {
            $args['--insecure'] = true;
        }
        if ($year = $this->input->getOption('year')) {
            $args['--year'] = $year;
        }
        if ($cookie = $this->input->getOption('cookie')) {
            $args['--cookie'] = $cookie;
        }
        $input = new ArrayInput($args);
        return $command->run($input, $this->output);
    }

    protected function copyFiles(array $mapping, int $day): bool
    {
        foreach ($mapping as $source => $destination) {
            $sourceFilename = realpath(__DIR__ . "./{$source}");
            $destinationFilename = __DIR__ . "/../../{$destination}";
            $data = file_get_contents($sourceFilename);
            $data = str_replace('{:day}', $day, $data);
            file_put_contents($destinationFilename, $data);
            $this->output->writeln("Created <info>{$destination}</>");
        }
        return true;
    }

    protected function checkExistingFiles(array $mapping): bool
    {
        $existing = array_filter($mapping, function(string $filename): bool {
            $filename = realpath(__DIR__ . "/../../{$filename}");
            return file_exists($filename);
        });
        if (!$existing) {
            return true;
        }

        $this->io->warning("The following files already exist:");
        foreach ($existing as $filename) {
            $this->io->writeln("  - {$filename}");
        }
        $this->io->writeln('');
        $answer = $this->io->confirm("Are you sure you want to overwrite these?", false);
        if (!$answer) {
            $this->io->writeln("<info>OK, nothing to do</>");
            return false;
        }
        foreach ($existing as $filename) {
            $this->output->writeln("Deleting <comment>{$filename}</>");
            unlink($filename);
        }
        return true;
    }

    protected function getDay(): ?int
    {
        $day = (int) $this->input->getArgument('number');
        if ($day < 1 || $day > 25) {
            $this->io->error("Day must be between 1 and 25 inclusive");
            return null;
        }
        return $day;
    }

    protected function getMapping(int $day): array
    {
        return array_map(function(string $filename) use ($day): string {
            return str_replace('{:day}', $day, $filename);
        }, self::FILE_MAPPING);
    }
}