<?php

namespace Donchev\Framework\Controller\Web;

use DI\Annotation\Inject;
use Donchev\Framework\Flash\FlashService;

abstract class FlashAwareController extends AbstractController
{
    private const FLASH_KEY = 'flashes';

    /**
     * @Inject()
     * @var FlashService
     */
    private $flashService;

    public function renderTemplate(string $templateName, array $parameters = [])
    {
        $parameters[self::FLASH_KEY] = $this->flashService->getFlashes();

        parent::renderTemplate($templateName, $parameters);
    }

    public function getFlashService(): FlashService
    {
        return $this->flashService;
    }
}
