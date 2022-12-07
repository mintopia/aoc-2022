<?php
namespace Mintopia\Aoc2022\Meta;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeInput extends Command
{
    protected const USER_AGENT = 'github.com/mintopia jess@mintopia.net';
    protected const DEFAULT_YEAR = 2022;


    protected static $defaultName = 'make:input';
    protected static $defaultDescription = 'Fetch input from the Advent of Code website';

    protected InputInterface $input;
    protected OutputInterface $output;
    protected SymfonyStyle $io;

    protected function configure()
    {
        $this->addArgument('day', InputArgument::REQUIRED, 'The day to fetch input for. Use all for all input for that year');
        $this->addOption('year', 'y', InputOption::VALUE_OPTIONAL, 'The year to fetch input for', self::DEFAULT_YEAR);
        $this->addOption('insecure', 'i', InputOption::VALUE_NONE, "Don't verify TLS certificates");
        $this->addOption('cookie', 'c', InputOption::VALUE_OPTIONAL, 'Your adventofcode.com session cookie value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Advent Of Code Input Downloader');

        if (!$cookie = $this->getCookie()) {
            $this->io->error("You need to provide the adventofcode.com session cookie");
            return self::FAILURE;
        }

        $year = $this->input->getOption('year');
        $day = $this->input->getArgument('day');
        $verify = !$this->input->getOption('insecure');
        if (!$verify) {
            $this->io->warning('TLS certificates will not be verified');
        }

        if ($day === 'all') {
            for ($i = 1; $i <= $this->getMaxDay($year); $i++) {
                $this->downloadDay($year, $i, $cookie, $verify);
                sleep(1);
            }
        } else {
            $day = (int)$day;
            if ($day < 1 || $day > $this->getMaxDay($year)) {
                $this->io->error("Day is not valid");
                return self::FAILURE;
            }

            $this->downloadDay($year, $day, $cookie, $verify);
        }

        return self::SUCCESS;
    }

    protected function downloadDay(int $year, int $day, string $cookie, bool $verify)
    {
        $filename = __DIR__ . "/../../input/day{$day}.txt";
        if (file_exists($filename)) {
            $overwrite = $this->io->confirm("Input for day {$day} already exists, do you want to overwrite it?", false);
            if (!$overwrite) {
                return;
            }
        }

        $url = "https://adventofcode.com/{$year}/day/{$day}/input";
        $this->io->writeln("Downloading <comment>{$url}</>");

        $client = new Client();
        $response = $client->get($url, [
            'headers' => [
                'Cookie' => $cookie,
                'User-Agent' => self::USER_AGENT,
            ],
            'verify' => $verify,
        ]);
        $content = (string) $response->getBody();

        file_put_contents($filename, $content);

        $this->io->writeln("Saved input for day {$day} to <comment>input/day{$day}.txt</>");
    }

    protected function getMaxDay(int $year): int
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $start = $now->setTime(5, 0);

        for ($i = 25; $i >= 1; $i--) {
            $toCheck = $start->setDate($year, 12, $i);
            if ($toCheck < $now) {
                return $i;
            }
        }
        return 0;
    }

    protected function getCookie(): ?string
    {
        $cookie = $this->input->getOption('cookie');
        if (!$cookie) {
            $cookie = $_ENV['AOC_SESSION_COOKIE'] ?? null;
        }
        if (!$cookie) {
            $cookie = $this->io->askQuestion("Please enter your adventofcode.com session cookie value");
        }
        if (!$cookie) {
            return null;
        }
        return "session={$cookie}";
    }
}