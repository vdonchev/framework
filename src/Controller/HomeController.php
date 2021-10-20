<?php

namespace Donchev\Framework\Controller;

use MeekroDB;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HomeController extends BaseController
{
    public function index(LoggerInterface $logger, MeekroDB $db)
    {
//        $users = $db->query("SELECT * FROM dz_users");
//        var_dump($users);

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
