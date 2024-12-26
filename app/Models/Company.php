<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "companies";
    protected $fillable = [
        'uuid',
        'name',
        'status',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'status'       => 'nullable|in:Active,In-active',
        ];
    }

   
}
