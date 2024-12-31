<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakDownProblemNote extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'note',
        'status',
        'company_id',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
    ];

    public static function validationRules()
    {
        return [
            'company_id'              => 'required|exists:companies,id',
            'note'                    => 'required|string',
            'status'                  => 'nullable|in:Active,Inactive',
            'creator_id'              => 'nullable',
            'creator_type'            => 'nullable',
            'updater_id'              => 'nullable',
            'updater_type'            => 'nullable',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
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
