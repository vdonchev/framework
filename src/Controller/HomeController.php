<?php

namespace Donchev\Framework\Controller;

class HomeController extends BaseController
{
    public function index()
    {
        var_dump($this->getSettings('env'));
        var_dump('Hello!');
    }
}
