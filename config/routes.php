<?php

use Donchev\Framework\Controller\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
];
