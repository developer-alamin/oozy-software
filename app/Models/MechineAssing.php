<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MechineAssing extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';

    protected $casts = [
        'model_id'=> 'integer'
    ];

    protected $fillable = [
        'name',
        'factory_id',
        'brand_id',
        'model_id',
        'machine_type_id',
        'machine_source_id',
        'supplier_id',
        'rent_date',
        'rent_name',
        'rent_note',
        'rent_amount_type',
        'machine_code',
        'partial_maintenance_day',
        'full_maintenance_day',
        'purchase_price',
        'purchase_date',
        'note',
        'status',
        'machine_status_id',
        'qr_code_path'
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
    public function machineStatus()
    {
        return $this->belongsTo(MachineStatus::class, 'machine_status_id');
    }
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class, 'model_id');
    }
    public function mechineType()
    {
        return $this->belongsTo(MechineType::class, 'machine_type_id');
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

    public function movements()
    {
        return $this->hasMany(Movement::class, 'machine_id', 'id');
    }


}