<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Line extends Model
{

    use HasFactory,SoftDeletes;

    protected $table = 'lines';
    // protected $primaryKey = 'id';


    protected $fillable = [
        'uuid',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
        'unit_id',
        'name',
        'status',
        'description',
    ];

    protected $casts = [
        'unit_id'    => 'integer', // Casts factory_id to an integer
        'uuid'       => 'string',
        'id'         => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];

    public function units()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
    // Polymorphic relationships
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
        return $this->belongsToMany(Line::class, 'line_unit', 'unit_id', 'line_id')
                    ->withPivot('unit_id', 'line_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
}
