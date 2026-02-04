<?php

namespace App\Support;

class DepartmentContext
{
    public static function id(): int
    {
        // TEMP: single department (TCRC), education departmemnt -2, finance department -3
        
        return 2;
    }
}
