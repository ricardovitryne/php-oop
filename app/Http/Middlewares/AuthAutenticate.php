<?php
require_once __DIR__ . '/Middleware.php';
require_once __DIR__ . '/../Response/Response.php';
require_once __DIR__.'/../Controllers/Auth/AuthControlller.php';

class AuthAutenticate implements Middleware
{
    public function run()
    {
        $token = $_SERVER['HTTP_TOKEN'] ?? null;

        if (!$token) {
            Response::toJSON(['ERROR' => 'Unauthenticated'], 403);
        }

        $user = new User();

        $auth = $user->select('iduser, token_expiration')
                     ->findByColumn('token', $token)[0] ?? [];

        $limit = date('Y-m-d H:i:s');

        if (!$auth || ($auth['token_expiration'] < $limit)) {
            Response::toJSON(['ERROR' => 'Token invalid'], 403);
        }

        AuthControlller::refreshToken($auth['iduser']);
    }
}