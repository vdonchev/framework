<?php

declare(strict_types=1);

namespace Donchev\Framework\Controller\Api;

use Donchev\Framework\Controller\Web\AbstractController;
use Donchev\Framework\Exception\FrameworkException;

abstract class AbstractApiController extends AbstractController
{
    /**
     * @throws FrameworkException
     */
    public function authorizeApiCall()
    {
        $auth_user = $_SERVER['PHP_AUTH_USER'] ?? '';
        $auth_pass = $_SERVER['PHP_AUTH_PW'] ?? '';

        if (!$this->validateAuthentication($auth_user, $auth_pass)) {
            throw new FrameworkException('Authentication failed.');
        }

        return $this->getPayload();
    }

    public function sendJsonResponse($payload): void
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        die;
    }

    private function validateAuthentication(string $user, string $pass): bool
    {
        return hash_equals($this->getContainer()->get('app.settings')['api']['username'], $user) &&
            hash_equals($this->getContainer()->get('app.settings')['api']['password'], $pass);
    }

    /**
     * @throws FrameworkException
     */
    private function getPayload(): mixed
    {
        $payload = $this->readInput();
        if (!$data = json_decode($payload, true)) {
            throw new FrameworkException('Invalid payload.');
        }

        return $data;
    }

    protected function readInput(): string
    {
        return file_get_contents('php://input');
    }
}
