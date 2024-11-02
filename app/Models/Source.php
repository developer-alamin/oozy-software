<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Source extends Model
{
    use HasFactory,SoftDeletes,HasUuids;
     public $incrementing = false; // Disable auto-incrementing for UUIDs
    protected $keyType = 'string'; // Set key type to string for UUIDs
    // Define the primary key for this model
    protected $primaryKey = 'uuid'; // Assuming you want the uuid as the primary key

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|max:255',
            'description'  => 'nullable',
            'status'       => 'nullable|in:Active,Inactive',
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

    public static function restoreBrand($id)
    {
        // Find the brand by id in the trashed records
        $brand = self::onlyTrashed()->find($id);
        // If no brand is found, return false
        if (!$brand) {
            return false;
        }
        // Restore the brand
        $brand->restore();
        return true;
    }

}
