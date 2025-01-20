<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class MechineType extends Model
{
    use HasFactory,SoftDeletes;

    // public $incrementing = false; // Disable auto-incrementing for UUIDs
    //protected $table = "mechine_types";
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'name',
        'day',
        'partial_maintenance_day',
        'full_maintenance_day',
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
            'company_id'               => 'required|exists:companies,id',
            'name'                     => 'required|max:255',
            'day'                      => 'nullable',
            'partial_maintenance_day'  => 'nullable',
            'full_maintenance_day'     => 'nullable',
            'description'              => 'nullable',
            'status'                   => 'nullable|in:Active,Inactive',
        ];
    }

    // Polymorphic relationships
    public function creator()
    {
        return $this->morphTo();
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
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
    public function requisitionDetails()
    {
        return $this->hasMany(RequisitionDetails::class);
    }

    
}