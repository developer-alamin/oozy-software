<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Rent extends Model
{
    use HasFactory,SoftDeletes,HasUuids;
    protected $table = 'rents';
   
    // Assuming you want the uuid as the primary key
    protected $primaryKey = 'uuid'; 
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'photo',
        'address',
        'description',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
    ];

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

    // Polymorphic relationships
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
}
