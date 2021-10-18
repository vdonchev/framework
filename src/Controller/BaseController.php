<?php

namespace Donchev\Framework\Controller;

use DI\Annotation\Inject;
use DI\Container;

abstract class BaseController
{
    /**
     * @Inject()
     * @var Container
     */
    private $container;

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getSettings(string $name)
    {
        return $this->container->get('settings')[$name];
    }
}
