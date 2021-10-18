<?php

namespace Donchev\Framework\Controller;

class HomeController extends BaseController
{
    public function index()
    {
        $this->renderTemplate('home/index.html.twig');
    }
}
