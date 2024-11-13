<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{

    use HasFactory,SoftDeletes;

    protected $table = 'groups';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
        'technician_id',
        'name',
        'description',
        'status'
    ];

    public static function validationRules()
    {
        return [
            'technician_id' => 'required',
            'name'          => 'required|string|max:255',
            'status'        => 'nullable|string',
            'description'   => 'nullable|string',
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
    public function technicians()
    {
        return $this->belongsTo(Technician::class,'technician_id');
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