<?php

declare(strict_types=1);

namespace Donchev\Framework\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cache:clear', description: 'Clears the cache')]
class CacheClearCommand extends Command
{
    protected array $errors = [];

    public function __construct(string $name = null)
    {
        parent::__construct($name ?? self::getDefaultName());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = dirname(__DIR__, 2) . '/var/cache';

        $this->delete($path, $path);

        if (!empty($this->errors)) {
            $output->writeln('<fg=yellow>==> Cache partially cleared. Some files/directories could not be deleted:</>');
            foreach ($this->errors as $error) {
                $output->writeln("  <fg=red>- {$error}</>");
            }
        } else {
            $output->writeln('<fg=green>==> Cache fully cleared!</>');
        }

        return Command::SUCCESS;
    }

    protected function delete(string $dir, string $rootDir): void
    {
        foreach (glob($dir . '/*') ?: [] as $file) {
            if (is_dir($file)) {
                $this->delete($file, $rootDir);
            } else {
                if (!@unlink($file)) {
                    $this->errors[] = $file;
                }
            }
        }

        if ($dir !== $rootDir && is_dir($dir)) {
            if (!@rmdir($dir)) {
                $this->errors[] = $dir;
            }
        }
    }
}
