<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'factory_id',
        'name',
        'type',
        'email',
        'phone',
        'photo',
        'address',
        'description',
        'status',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    // Type casting for specific attributes
    protected $casts = [
      'uuid'       => 'string',
      'company_id' => 'integer',
      'factory_id' => 'integer',
      'creator_id' => 'integer',
      'updater_id' => 'integer',
      'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
      'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];
    // Polymorphic relationships
    public function creator()
    {
        return $this->morphTo();
    }
    public function updater()
    {
        return $this->morphTo();
    }

    public static function validationRules()
    {
        return [
            'uuid'          => 'nullable',
            'company_id'    => 'required|integer',
            'factory_id'    => 'required|integer',
            'name'          => 'required|string|max:255',
            'type'          => 'required|string|max:255',
            'email'         => 'nullable',
            'phone'         => 'nullable',
            'photo'         => 'nullable|string',
            'description'   => 'nullable|string',
            'address'       => 'nullable|string',
            'status'        => 'nullable',
            'creator_id'    => 'nullable',
            'creator_type'  => 'nullable',
            'updater_id'    => 'nullable',
            'updater_type'  => 'nullable',
            'meta_data'     => 'nullable',
        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
}