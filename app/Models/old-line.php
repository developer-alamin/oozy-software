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
        'name',
        'status',
        'description',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'status'       => 'nullable|string',
            'description'  => 'nullable|string',
        ];
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
}