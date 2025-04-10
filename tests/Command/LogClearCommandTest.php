<?php

declare(strict_types=1);

namespace Tests\Command;

use Donchev\Framework\Command\LogClearCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class LogClearCommandTest extends TestCase
{
    private string $logFile;

    protected function tearDown(): void
    {
        if (isset($this->logFile) && file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testLogFileNotFound(): void
    {
        $nonexistentFile = sys_get_temp_dir() . '/nonexistent_' . uniqid() . '.log';
        $command = new LogClearCommand($nonexistentFile);
        $tester = new CommandTester($command);

        $tester->execute([]);
        $output = $tester->getDisplay();

        self::assertStringContainsString('Log file not found', $output);
    }

    public function testLogFileClearedSuccessfully(): void
    {
        $this->logFile = tempnam(sys_get_temp_dir(), 'log_');
        file_put_contents($this->logFile, 'some log content');

        $command = new LogClearCommand($this->logFile);
        $tester = new CommandTester($command);

        $tester->execute([]);
        $output = $tester->getDisplay();

        self::assertStringContainsString('Log file cleared successfully', $output);
        self::assertSame('', file_get_contents($this->logFile));
    }
}
