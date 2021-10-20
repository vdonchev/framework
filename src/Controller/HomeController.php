<?php

namespace Donchev\Framework\Controller;

use DB;
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class HomeController extends BaseController
{
    public function index(LoggerInterface $logger)
    {
        $res = DB::query('SELECT * FROM dz_users');
        var_dump($res);

        $logger->info(123);

        //var_dump($mailer);

        /** @var CacheInterface $cache */
        $cache = $this->getCacheAdapter();

        $test = $cache->get('test', function (CacheItemInterface $item) {
            $item->expiresAfter(5);

            return 10 + rand(0, 10);
        });

        $this->renderTemplate('home/index.html.twig', ['test' => $test]);
    }
}
