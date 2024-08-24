<?php

namespace App\Model;

class Database
{
    public static function init()
    {
        new CreateTables();
    }
}
