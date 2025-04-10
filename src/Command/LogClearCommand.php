<?php

declare(strict_types=1);

namespace Donchev\Framework\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'log:clear', description: 'Clears the log')]
class LogClearCommand extends Command
{
    public function __construct(private readonly string $logFile)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!file_exists($this->logFile)) {
            $output->writeln("<fg=yellow>==> Log file not found at: {$this->logFile}</>");
            return Command::SUCCESS;
        }

        if (!is_writable($this->logFile)) {
            $output->writeln("<fg=red>==> Cannot write to log file: {$this->logFile}</>");
            return Command::FAILURE;
        }

        try {
            file_put_contents($this->logFile, '');
            $output->writeln('<fg=green>==> Log file cleared successfully!</>');
        } catch (\Throwable $e) {
            $output->writeln('<fg=red>==> Failed to clear log file: ' . $e->getMessage() . '</>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
