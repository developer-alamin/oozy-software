<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownService extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'id';

    protected $fillable = [
        'machine_id',
        'location',
    ];

    public static function validationRules()
    {
        return [
            'break_down_problem_note' => 'required|string',
            'status'                  => 'nullable|in:Active,Inactive',
            'creator_id'              => 'nullable',
            'creator_type'            => 'nullable',
            'updater_id'              => 'nullable',
            'updater_type'            => 'nullable',
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
