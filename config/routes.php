<?php

use Donchev\Framework\Controller\Web\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
];
