<?php
require_once __DIR__ . '/../Controllers/Controller.php';
require_once __DIR__ . '/../../Model/User.php';
require_once __DIR__ . '/../../Model/UserDrink.php';
require_once __DIR__ . '/../../Http/Response/Response.php';

class UserController extends Controller
{
    protected $connection;

    /**
     * Get all users paginate
     * @param $request
     * @return void
     */
    public static function index($request = null)
    {
        $page = (isset($request->page) && intval($request->page) > 0)
            ? intval($request->page)
            : 1;

        $user = (new User())->select('iduser, name, email, drinkcounter')
                            ->paginate($page, 25)
                            ->findAll();

        Response::toJSON($user);
    }

    /**
     * Display one user find by id
     * @param $iduser
     * @param $request
     * @return void
     */
    public static function show($iduser, $request = null)
    {
        $iduser = (int) $iduser;

        if (!$iduser) {
            Response::toJSON(['Error' => 'User ID is required'], 422);
        }

        $user = (new User())->select('iduser, name, email, drinkcounter')
                            ->find($iduser);

        Response::toJSON($user);
    }

    /**
     * Save new user into database
     * @param $request
     * @return void
     */
    public static function store($request)
    {
        self::validateRequest($request);

        $user = new User();

        $exists = $user->findByColumn('email', $request->email);

        if ($exists) {
            Response::toJSON(['Error' => 'User already registered'], 422);
        }

        $request->password = md5($request->password);

        $return = $user->insert((array) $request);

        $return
            ? Response::toJSON(['Success' => 'User registered'])
            : Response::toJSON(['Error' => 'Error saving user'], 500);
    }

    /**
     * Update User
     * @param $iduser
     * @param $request
     * @return void
     */
    public static function update($iduser, $request = null)
    {
        $iduser = (int) $iduser;

        if (!$iduser) {
            Response::toJSON(['Error' => 'User ID is required'], 422);
        }

        $user = new User();

        $update = [
            'name'     => $request->name ?? null,
            'email'    => $request->email ?? null,
            'password' => isset($request->password) ? md5($request->password) : null,
        ];

        $update = array_filter($update);

        if (!$update) {
            Response::toJSON(['Error' => 'Send something to change'], 400);
        }

        $updated = $user->update($iduser, $update);

        $updated
            ? Response::toJSON(['Success' => 'User updated'])
            : Response::toJSON(['Error' => 'Nothing to update'], 400);
    }

    /**
     * Delete user
     * @param $iduser
     * @return void
     */
    public static function delete($iduser)
    {
        $iduser = (int) $iduser;

        if (!$iduser) {
            Response::toJSON(['Error' => 'User ID is required'], 422);
        }

        $user = new User();

        $deleted = $user->delete($iduser);

        $deleted
            ? Response::toJSON(['Success' => 'User deleted'])
            : Response::toJSON(['Error' => 'User not found'], 404);
    }

    /**
     * @param $request
     * @return void
     */
    public static function validateRequest($request)
    {

        if (!isset($request->name)) {
            $messages[] = ['name' => 'Field name is required'];
        }

        if (!isset($request->email)) {
            $messages[] = ['email' => 'Field email is required'];
        }

        if (!isset($request->password)) {
            $messages[] = ['password' => 'Field password is required'];
        }

        if ($messages ?? []) {
            Response::toJSON(['Errors' => $messages], 422);
        }
    }
}