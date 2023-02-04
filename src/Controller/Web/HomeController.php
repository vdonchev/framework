<?php

namespace Donchev\Framework\Controller\Web;

use DI\DependencyException;
use DI\NotFoundException;
use Donchev\Framework\Flash\FlashService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends BaseController
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index(FlashService $flashService)
    {
        $flashService->addInfo('info');
        $flashService->addSuccess('success');
        $flashService->addError('error');
        $flashService->addWarning('warning');

        $this->renderTemplate('home/index.html.twig');
    }
}
