<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Floor extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'uuid',
        'name',
        'description',
        'status',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
        'factory_id',
    ];
    protected $casts = [
        'factory_id' => 'integer', // Casts factory_id to an integer
        'uuid'       => 'string',
        'id'         => 'integer',
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

    public function factories()
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function units()
    {
        return $this->hasMany(Unit::class);
    }
    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }

}
