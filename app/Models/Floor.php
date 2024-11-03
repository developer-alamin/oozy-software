<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

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
    ];


    public static function validationRules()
    {
        return [
            'name'         => 'required|max:255',
            'description'  => 'nullable',
            'status'       => 'nullable',

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
    public function factories(): BelongsToMany
    {
        return $this->belongsToMany(Factory::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class);
    }

}