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
        return $this->belongsToMany(Line::class, 'line_unit', 'unit_id', 'line_id')
                    ->withPivot('unit_id', 'line_id');
    }

}