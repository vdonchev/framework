<?php

namespace Donchev\Framework\Controller\Api;

use Donchev\Framework\Controller\Web\AbstractController;
use Donchev\Framework\Exception\AppException;

abstract class AbstractApiController extends AbstractController
{
    /**
     * @throws AppException
     */
    public function authorizeApiCall()
    {
        $auth_user = $_SERVER['PHP_AUTH_USER'] ?? '';
        $auth_pass = $_SERVER['PHP_AUTH_PW'] ?? '';

        if (!$this->validateAuthentication($auth_user, $auth_pass)) {
            throw new AppException('Authentication failed.');
        }

        return $this->getPayload();
    }

    public function sendJsonResponse($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        die;
    }

    private function validateAuthentication(string $user, string $pass): bool
    {
        return hash_equals($this->getContainer()->get('app.settings')['api.username'], $user) &&
            hash_equals($this->getContainer()->get('app.settings')['api.password'], $pass);
    }

    /**
     * @throws AppException
     */
    private function getPayload()
    {
        $payload = file_get_contents('php://input');

        if (!$data = json_decode($payload ?? '', true)) {
            throw new AppException('Invalid payload.');
        }

        return $data;
    }
}
