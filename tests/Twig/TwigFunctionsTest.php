<?php

declare(strict_types=1);

namespace Tests\Twig;

use Donchev\Framework\Flash\FlashService;
use Donchev\Framework\Twig\TwigFunctions;
use PHPUnit\Framework\TestCase;

final class TwigFunctionsTest extends TestCase
{
    public function testGetFlashesReturnsFlashServiceData(): void
    {
        $mockFlashes = ['info' => ['saved'], 'danger' => ['error']];

        $flashService = $this->createMock(FlashService::class);
        $flashService->expects(self::once())
            ->method('getFlashes')
            ->willReturn($mockFlashes);

        $twigFunctions = new TwigFunctions($flashService);
        $result = $twigFunctions->getFlashes();

        self::assertSame($mockFlashes, $result);
    }
}
