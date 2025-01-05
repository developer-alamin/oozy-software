<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "actions";
    protected $primaryKey = 'id'; // Assuming you want the uuid as the primary key
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'company_id',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    public static function validationRules()
    {
        return [
            'company_id'   => 'required|exists:companies,id',
            'name'         => 'required|string|max:255',
            'status'       => 'nullable|in:Active,Inactive',
        ];
    }

    public function company()
    {
        return $this->belongsTo( Company::class, 'company_id');
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
