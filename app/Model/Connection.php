<?php

class Connection
{
    private const HOST     = 'mysql8';
    private const DB       = 'php_db';
    private const USERNAME = 'root';
    private const PASSWORD = 'root';
    protected $connection = null;

    public function __construct()
    {
        try {
            $this->connection = new PDO('mysql:host=' . self::HOST . ';dbname=' . self::DB, self::USERNAME, self::PASSWORD);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function get()
    {
        return $this->connection;
    }
}
