<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductModel extends Model
{
    use HasFactory,SoftDeletes,HasUuids;

    public $incrementing = false; // Disable auto-incrementing for UUIDs
    protected $keyType = 'string'; // Set key type to string for UUIDs
    // Define the primary key for this model
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'name',
        'model_number',
        'description',
        'status',
        'meta_data',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'model_number' => 'required|string|max:255|',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
        ];
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
