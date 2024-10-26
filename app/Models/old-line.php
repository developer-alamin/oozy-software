<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class line extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'lines';

    protected $fillable = [
        'name',
        'number',
        'description', 
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'number'       => 'required|string',
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
}
