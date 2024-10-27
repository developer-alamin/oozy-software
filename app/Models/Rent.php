<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rent extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'rents';

    protected $fillable = ['name','email','phone','photo','address','description'];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
        ];
    }

    public static function restoreGroup($id)
    {
        // Find the group by id in the trashed records
        $rent = self::onlyTrashed()->find($id);

        // If no group is found, return false
        if (!$rent) {
            return false;
        }

        // Restore the group
        $rent->restore();

        return true;
    }
}
