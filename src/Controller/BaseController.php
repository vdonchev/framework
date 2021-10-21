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
     * @var Environment|null
     */
    private $template;

    /**
     * @param string $templateName
     * @param array $parameters
     * @throws DependencyException
     * @throws LoaderError
     * @throws NotFoundException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderTemplate(string $templateName, array $parameters = [])
    {
        echo $this->getTemplate()->render($templateName, $parameters);
    }

    /**
     * @param string $name
     * @return string
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getSettings(string $name): string
    {
        return $this->container->get('app.settings')[$name];
    }

    /**
     * @return AdapterInterface
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getCache(): AdapterInterface
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
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function getTemplate(): Environment
    {
        if (!$this->template) {
            $this->template = $this->container->get(Environment::class);
        }

        return $this->template;
    }
}
