<?php

namespace Donchev\Framework\Controller;

use DI\Annotation\Inject;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class BaseController
{
    /**
     * @Inject()
     * @var Container
     */
    private $container;

    /**
     * @Inject
     * @var Environment
     */
    private $template;

    /**
     * @param string $templateName
     * @param array $parameters
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderTemplate(string $templateName, array $parameters = [])
    {
        echo $this->template->render($templateName, $parameters);
    }

    /**
     * @param string $name
     * @return array
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getSettings(string $name)
    {
        return $this->container->get('app.settings')[$name];
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->container->get('app.cache');
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return Environment
     */
    public function getTemplate(): Environment
    {
        return $this->template;
    }
}
