<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'model_number', 'description', 'status','meta_data'];
    
    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'model_number' => 'required|string|max:255|',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
        ];
    }
}