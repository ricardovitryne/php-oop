<?php
require_once __DIR__ . '/Middlewares/AuthAutenticate.php';

class Route extends AuthAutenticate
{

    private static function checkMiddlewares($middlewares)
    {
        foreach ($middlewares as $middleware) {
            (new $middleware)->run();
        }
    }

    public static function get($route, $action, $middlewares)
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'GET') {
            return;
        }

        self::processRoute($route, $action, $middlewares);
    }

    /**
     * @param $route
     * @param $action
     * @param $middlewares
     * @return void
     */
    public static function post($route, $action, $middlewares = [])
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
            return;
        }

        self::processRoute($route, $action, $middlewares);
    }

    /**
     * @param $route
     * @param $action
     * @param $middlewares
     * @return void
     */
    public static function put($route, $action, $middlewares = [])
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'PUT') {
            return;
        }

       // self::checkMiddlewares($middlewares);
        self::processRoute($route, $action, $middlewares);
    }

    /**
     * @param $route
     * @param $action
     * @param $middlewares
     * @return void
     */
    public static function delete($route, $action, $middlewares = [])
    {
        if (strtoupper($_SERVER['REQUEST_METHOD']) != 'DELETE') {
            return;
        }

      //  self::checkMiddlewares($middlewares);
        self::processRoute($route, $action, $middlewares);
    }

    /**
     * @return false|string
     */
    private static function getParamRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {

            case 'POST' :
            case 'PUT':
                $parm = trim(file_get_contents("php://input"));
                break;
            case 'GET':
                $parm = json_encode($_GET);
                break;
        }

        return $parm ?? json_encode([]);
    }

    private static function processRoute($route, $callback, $middlewares = [])
    {
        $requestUrl      = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $requestUrl      = strtok($requestUrl, '?');
        $routeSegments   = explode('/', $route);
        $requestSegments = explode('/', $requestUrl);
        array_shift($routeSegments);
        array_shift($requestSegments);

        //check if quantity segments are equal
        if (count($routeSegments) != count($requestSegments)) {
            return;
        }

        self::checkMiddlewares($middlewares);

        $routeParms = [];
        foreach ($routeSegments as $i => $routeSegment) {
            if (preg_match("/\{(.*?)}/", $routeSegment, $param)) {
                $routeParms[$param[1]] = $requestSegments[$i];
            } else if ($routeSegment != $requestSegments[$i]) {
                return;
            }
        }

        if (is_callable($callback)) {
            //add params query or body
            $parms = json_decode(self::getParamRequest());
            if ($parms) {
                $routeParms['request'] = $parms;
            }
            $callback(...$routeParms);
        }
        exit();
    }
}