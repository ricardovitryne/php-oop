<?php
require_once __DIR__ . '/../Controller.php';

class AuthControlller extends Controller
{
    /**
     * @param $request
     * @return void
     */
    public static function login($request = null)
    {
        if (!isset($request->email)) {
            $messages[] = ['email' => 'Field email is required'];
        }

        if (!isset($request->password)) {
            $messages[] = ['password' => 'Field password is required'];
        }

        if ($messages ?? []) {
            Response::toJSON(['Errors' => $messages], 422);
        }

        $user = new User();
        $auth = $user->query('SELECT * FROM users WHERE email = ? LIMIT 1', [$request->email])[0] ?? [];

        if (!$auth) {
            Response::toJSON(['Errors' => 'User not found'], 404);
        }

        $matched = $auth['password'] == md5($request->password);

        if (!$matched) {
            Response::toJSON(['Errors' => 'Password invalid'], 403);
        }

        $accessToken = sha1(date('d/m/Y H:i:s') . $auth['iduser']);

        $date = new DateTime();
        $date->add(new DateInterval('P2D'));
        $expiration = $date->format('Y-m-d H:i:s');
        $user->update($auth['iduser'], ['token' => $accessToken, 'token_expiration' => $expiration]);

        unset($auth['password']);
        unset($auth['token_expiration']);
        $return = array_merge($auth, ['token' => $accessToken]);

        Response::toJSON($return);
    }

    /**
     * @return void
     */
    public static function logoff()
    {
        $user = new User();

        $token = $_SERVER['HTTP_TOKEN'] ?? null;

        $auth = $user->select('iduser, token_expiration')
                     ->findByColumn('token', $token)[0] ?? [];

        $return = $user->update($auth['iduser'], ['token' => null, 'token_expiration' => null]);

        $return ?
            Response::toJSON(['Success' => 'Logoff success'])
            : Response::toJSON(['Errors' => 'Failed logoff'], 500);
    }

    /**
     * @param $idUser
     * @return void
     */
    public static function refreshToken($idUser)
    {
        $user = new User();
        $date = new DateTime();
        $date->add(new DateInterval('P2D'));
        $user->update($idUser, ['token_expiration' => $date->format('Y-m-d H:i:s')]);
    }
}