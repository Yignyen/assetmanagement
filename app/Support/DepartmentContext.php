<?php

namespace App\Support;

class DepartmentContext
{
    public static function id(): int
    {
        // TEMP: single department (TCRC), education departmemnt -2, finance department -3
        
        return 1;
    }
    




    public static function code(): string
    {
        return match (self::id()) {
            1 => 'TCRC',
            2 => 'EDU',
            3 => 'FIN',
            default => 'GEN',
        };
    }
}


