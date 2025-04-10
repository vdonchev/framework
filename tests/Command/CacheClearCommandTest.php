<?php

declare(strict_types=1);

namespace Tests\Command;

use Donchev\Framework\Command\CacheClearCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class CacheClearCommandTest extends TestCase
{
    private string $testCachePath;

    protected function setUp(): void
    {
        $this->testCachePath = sys_get_temp_dir() . '/test_cache_' . uniqid();
        mkdir($this->testCachePath . '/subdir', 0777, true);
        file_put_contents($this->testCachePath . '/file.txt', 'data');
        file_put_contents($this->testCachePath . '/subdir/file2.txt', 'more data');

        // Override the actual cache path used in the command
        putenv('CACHE_PATH_OVERRIDE=' . $this->testCachePath);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testCachePath)) {
            $it = new \RecursiveDirectoryIterator($this->testCachePath, \FilesystemIterator::SKIP_DOTS);
            $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
            }
            rmdir($this->testCachePath);
        }
        putenv('CACHE_PATH_OVERRIDE');
    }

    public function testExecuteDeletesAllCacheFiles(): void
    {
        $command = new class () extends CacheClearCommand {
            protected function execute(InputInterface $input, OutputInterface $output): int
            {
                $this->delete(getenv('CACHE_PATH_OVERRIDE'), getenv('CACHE_PATH_OVERRIDE'));
                if (!empty($this->errors)) {
                    $output->writeln('Partial clear');
                } else {
                    $output->writeln('Full clear');
                }
                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $output = $tester->getDisplay();

        self::assertStringContainsString('Full clear', $output);
        self::assertFileDoesNotExist($this->testCachePath . '/file.txt');
        self::assertFileDoesNotExist($this->testCachePath . '/subdir/file2.txt');
    }
}
