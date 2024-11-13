<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
        'meta_data',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'nullable|in:Active,Inactive',
            'meta_data'    => 'nullable',
            'creator_id'   => 'nullable',
            'creator_type' => 'nullable',
            'updater_id'   => 'nullable',
            'updater_type' => 'nullable',
        ];
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
