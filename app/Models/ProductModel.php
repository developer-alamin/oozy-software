<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ProductModel extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'description',
        'status',
        'type',
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
            'type'         => 'required|string|max:255|',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
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