<?php

namespace Donchev\Framework\Controller\Web;

use DI\DependencyException;
use DI\NotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends AbstractController
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index()
    {
        $this->renderTemplate('home/index.html.twig');
    }
}
