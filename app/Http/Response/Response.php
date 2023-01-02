<?php

class Response
{
    public static function toJSON($data = [], $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}
