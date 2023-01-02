<?php
require_once  __DIR__.'/../Model/Connection.php';
require_once  __DIR__.'/../Model/Model.php';

class User extends Model
{
    protected $table = 'users';
    protected $tableKey = 'iduser';
}