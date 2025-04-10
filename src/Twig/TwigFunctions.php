<?php

declare(strict_types=1);

namespace Donchev\Framework\Twig;

use Donchev\Framework\Flash\FlashService;

class TwigFunctions
{
    private FlashService $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFlashes(): array
    {
        return $this->flashService->getFlashes();
    }
}
