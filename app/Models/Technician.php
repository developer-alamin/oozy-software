<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'photo',
        'address',
        'description',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
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
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|max:255',
            'phone'         => 'required|string|max:25',
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


    // public function updater()
    // {
    //     return $this->morphTo();
    // }
}