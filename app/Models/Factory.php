<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Factory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'factory_code',
        'factory_owner',
        'factory_size',
        'factory_capacity',
        'email',
        'phone',
        'location',
        'status',
        'note',
    ];
    protected $casts = [
        'uuid'       => 'string',
        'id'         => 'integer',
        'company_id' => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function floors()
    {
        return $this->hasMany(Floor::class);
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
