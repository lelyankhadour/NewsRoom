<?php

namespace App\Enums;

enum UserRole:string
{
    //
    case  ADMIN="admin";
    case WRITER="writer";
    case READER ='reader';
    //get all values as array to pass it to migration 
    public static function values():array
    {
        return array_column(self::cases(),"value");
        }
}
