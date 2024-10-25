<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Group extends Model
{
   use HasFactory,SoftDeletes;
    protected $fillable = ['name','description','deleted_at'];
    
    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
        ];
    }
     public static function restoreGroup($id)
    {
        // Find the group by id in the trashed records
        $group = self::onlyTrashed()->find($id);

        // If no group is found, return false
        if (!$group) {
            return false;
        }

        // Restore the group
        $group->restore();

        return true;
    }
}
