<?php

declare(strict_types=1);

use Donchev\Framework\Controller\Web\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
];
