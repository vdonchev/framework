<?php

namespace Donchev\Framework\Controller;

use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HomeController extends BaseController
{
    public function index(LoggerInterface $logger)
    {
        $logger->info(123);
        /** @var CacheInterface $cache */
        $cache = $this->getCacheAdapter();

        $test = $cache->get('test', function (CacheItemInterface $item) {
            $item->expiresAfter(5);

            return 10 + rand(0, 10);
        });

        $this->renderTemplate('home/index.html.twig', ['test' => $test]);
    }
}
