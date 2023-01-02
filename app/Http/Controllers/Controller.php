<?php

class Controller
{
    protected $connection = null;

    public function __contruct()
    {
        $this->connection = (new Connection())->get();
    }
}