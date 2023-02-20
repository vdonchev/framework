<?php

namespace Donchev\Framework\Controller\Web;

use DI\DependencyException;
use DI\NotFoundException;
use Donchev\Framework\Flash\FlashService;
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
    public function index(FlashService $flashService)
    {
        $data = [
            'firstname' => '',
            'username' => 'bob',
            'address' => 'This is my address',
            'zipcode' => '999',
            'email' => 'jo@',
            'password' => 'test23',
            'password2' => 'test123',
            'age' => 60,
            'ip' => '213.214.65.21',
            'Facebook URL' => 'facebook.com',
            'favorite' => 6,
        ];

        $this->renderTemplate('home/index.html.twig');
    }
}
