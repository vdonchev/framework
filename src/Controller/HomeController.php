<?php

namespace Donchev\Framework\Controller;

use Psr\Log\LoggerInterface;

class HomeController
{
    public function index(LoggerInterface $logger)
    {
        $logger->info('test');
        var_dump('Hello!');
    }
}
