<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name','description', 'status','meta_data'];
    
    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
            'meta_data'    => 'nullable',
        ];
    }
}
