<?php

declare(strict_types=1);

namespace Donchev\Framework\Controller\Web;

use DI\Attribute\Inject;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class AbstractController
{
    #[Inject]
    private Container $container;

    private ?Environment $template = null;

    /**
     * @param string $templateName
     * @param array $parameters
     * @throws DependencyException
     * @throws LoaderError
     * @throws NotFoundException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderTemplate(string $templateName, array $parameters = []): void
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
        if (is_null($this->template)) {
            $this->template = $this->container->get(Environment::class);
        }

        return $this->template;
    }

    /**
     * @param string $url
     * @return void
     */
    public function redirect(string $url): void
    {
        header("Location: {$url}");
        exit();
    }
}
