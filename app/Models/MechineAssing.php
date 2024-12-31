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
        'product_model_id'=> 'integer'
    ];

    protected $fillable = [
        'name',
        'factory_id',
        'company_id',
        'brand_id',
        'product_model_id',
        'machine_type_id',
        'source_id',
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
        'qr_code_path',
        'serial_number',
        'commission_date',
        'warranty_period',
        'ownership',
        'power_requirements',
        'capacity',
        'dimensions',
        'machine_weight',
        'material_compatibility',
        'maximum_speed',
        'optimum_speed',
        'operating_temperature_range',
        'location_status',
        'tag',
        'line_id',
        'show_basic_details',
        'show_specifications',
        'machine_id'
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
    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id');
    }
    public function machineStatus()
    {
        return $this->belongsTo(MachineStatus::class, 'machine_status_id');
    }
    public function productModel()
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function mechineType()
    {
        return $this->belongsTo(MechineType::class, 'machine_type_id');
    }
    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }
    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
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