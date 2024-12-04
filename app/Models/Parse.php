<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parse extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';

    protected $fillable = [
        'company_id',
        'factory_id',
        'category_id',
        'supplier_name',
        'brand_name',
        'model_name',
        'parse_unit_id',
        'name',
        'quantity',
        'purchase_price',
        'purchase_date',
        'status',
        'note',
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
