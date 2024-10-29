<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Line extends Model
{
    use HasFactory,SoftDeletes,HasUuids;

    protected $table = 'lines';
    protected $primaryKey = 'uuid'; // Assuming you want the uuid as the primary key

    protected $fillable = [
        'uuid',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
        'name',
        'number',
        'description', 
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
