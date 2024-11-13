<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Brand extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id'; // Assuming you want the uuid as the primary key
    protected $fillable = [
        'uuid',
        'name',
        'type',
        'description',
        'status',
        'meta_data',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (empty($model->uuid)) {
    //             $model->uuid = (string) Str::uuid();
    //         }
    //     });
    // }

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:Mechine,Parse',
            'description'  => 'nullable|string',
            'status'       => 'nullable|in:Active,Inactive',
            'meta_data'    => 'nullable',

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
