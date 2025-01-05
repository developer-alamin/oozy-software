<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Effect extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "effects";
    protected $primaryKey = 'id'; // Assuming you want the uuid as the primary key
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'company_id',
        "cause_id",
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    public static function validationRules()
    {
        return [
            'cause_id'     => 'required|exists:causes,id',
            'name'         => 'required|string|max:255',
            'status'       => 'nullable|in:Active,Inactive',
        ];
    }

    public function company()
    {
        return $this->belongsTo( Company::class, 'company_id');
    } 
    public function cuase()
    {
        return $this->belongsTo( Cause::class, 'cause_id');
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
