<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Factory extends Model
{
    // public $incrementing = false; // Disable auto-incrementing for UUIDs
    // protected $keyType = 'string'; // Set key type to string for UUIDs
    // Define the primary key for this model
    // protected $primaryKey = 'id'; // Assuming you want the uuid as the primary key
    use HasFactory;

    protected $fillable = ['uuid','company_id', 'name', 'factory_code', 'email', 'phone', 'location', 'status'];

    public function floors(): BelongsToMany
    {
        return $this->belongsToMany(Floor::class);
    }
    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }

    public function lines()
    {
        return $this->belongsToMany(Line::class);
    }
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         if (empty($model->uuid)) {
    //             $model->uuid = (string) Str::uuid();
    //         }
    //     });
    // }
}