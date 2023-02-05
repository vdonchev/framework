<?php

namespace Donchev\Framework\Twig;

use Donchev\Framework\Flash\FlashService;

class TwigFunctions
{
    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFlashes()
    {
        return $this->flashService->getFlashes();
    }
}
