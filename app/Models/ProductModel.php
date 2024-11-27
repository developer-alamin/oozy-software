<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductModel extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';
  

    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'status',
        'type',
        'meta_data',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    
    protected $casts = [
        'uuid'       => 'string',
        'id'         => 'integer',
        'brand_id'   => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];

    public static function validationRules()
    {
        return [
            'brand_id'     => 'required',
            'name'         => 'required|string|max:255',
            'type'         => 'nullable|string|max:255|',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
        ];
    }
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id');
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