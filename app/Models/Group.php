<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Group extends Model
{
   use HasFactory,SoftDeletes,HasUuids;
    protected $fillable = [
        'name',
        'description',
        'deleted_at',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    
    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
        ];
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
