<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unit extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';


    protected $fillable = [
        'floor_id',
        'uuid',
        'name',
        'description',
        'status',
        'meta_data',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',

    ];

    protected $casts = [
        'floor_id'   => 'integer', // Casts factory_id to an integer
        'uuid'       => 'string',
        'id'         => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];

    public function floors()
    {
        return $this->belongsTo(Floor::class,'floor_id');
    }
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }

    public function lines()
    {
        return $this->hasMany(Line::class);
    }

    // public function lines()
    // {
    //     return $this->belongsToMany(Line::class, 'line_unit', 'unit_id', 'line_id')
    //                 ->withPivot('unit_id', 'line_id');
    // }

}