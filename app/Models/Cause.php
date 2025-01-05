<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cause extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "causes";
    protected $primaryKey = 'id'; // Assuming you want the uuid as the primary key
    protected $fillable = [
        'uuid',
        'name',
        'status',
        'company_id',
        "problem_note_id",
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    public static function validationRules()
    {
        return [
            'problem_note_id'   => 'required|exists:problem_notes,id',
            'name'              => 'required|string|max:255',
            'status'            => 'nullable|in:Active,Inactive',
        ];
    }

    public function company()
    {
        return $this->belongsTo( Company::class, 'company_id');
    } 
    public function problemNote()
    {
        return $this->belongsTo( ProblemNote::class, 'problem_note_id');
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
