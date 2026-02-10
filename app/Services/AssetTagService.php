<?php

namespace App\Services;

use App\Models\Asset;
use App\Support\DepartmentContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssetTagService
{
    public static function generate(): string
    {
        return DB::transaction(function () {  //database transaction - one asset at one time, two users clicku=ing together create solutions.

            $departmentId   = DepartmentContext::id();
            $departmentCode = DepartmentContext::code();

            $prefix  = $departmentCode . '-MLT-';
            $padding = 3;

            // Get last asset tag for THIS department only
            $lastTag = Asset::withTrashed()  //includes softdeleted assets,
                ->where('department_id', $departmentId) //only asset from this department
                ->where('asset_tag', 'LIKE', $prefix . '%') // and asset tags that starts with this prefix 
                ->orderBy('asset_tag', 'desc') //sorts matching assey tag from big to small , lattes and highest come first 
                ->lockForUpdate() //logs this row so no one can read or modify them until  i am done.(works only inside db:transaction)
                ->value('asset_tag'); //gets only top value

            if ($lastTag) { //cehcks last asset tag
                $number = (int) str_replace($prefix, '', $lastTag) + 1; // str_replace removes prefix and gets only number then converts number to interger  and incerment by +1
            } else { //for no macthing starts with 1
                $number = 1;
            }
// padding - 3 alwys,  001 '0' is character used to fill, add omn left 
            $tag = $prefix . str_pad($number, $padding, '0', STR_PAD_LEFT);//onverts a number into a fixed-length string by adding zeros on the left.
            Log::info('Generated Asset Tag',['tag'=>$tag]);
            return $tag;
        
        
            });
    }

    
}
