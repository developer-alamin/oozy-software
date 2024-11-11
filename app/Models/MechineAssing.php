<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MechineAssing extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id',
        'factory_id',
        'brand_id',
        'model_id',
        'mechine_type_id',
        'mechine_source_id',
        'supplier_id',
        'rent_id',
        'rent_date',
        'name',
        'mechine_code',
        'serial_number',
        'preventive_service_days',
        'purchace_price',
        'purchase_date',
        'status',
        'note',
        'mechine_status'
    ];

    // Optionally, cast some fields to specific types (e.g., dates)
    // protected $casts = [
    //     'rent_date'     => 'datetime',
    //     'purchase_date' => 'datetime',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }


}