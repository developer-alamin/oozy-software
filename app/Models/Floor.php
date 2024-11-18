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

    public static function validationRules()
    {
        return [
            'factory_id'   => 'required',
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

    public function factories()
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

}