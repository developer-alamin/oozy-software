<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operator extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'type',
        'email',
        'phone',
        'photo',
        'address',
        'description',
        'status',
        'creator_id',
        'creator_type',
        'updater_id',
        'updater_type',
    ];
    // Polymorphic relationships
    public function creator()
    {
        return $this->morphTo();
    }
    public function updater()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public static function validationRules()
    {
        return [
            'uuid'          => 'nullable',
            'company_id'    => 'required',
            'name'          => 'required|string|max:255',
            'type'          => 'required|string|max:255',
            'email'         => 'required|string|max:255',
            'phone'         => 'required|string|max:25',
            'photo'         => 'nullable|string',
            'description'   => 'nullable|string',
            'address'       => 'nullable|string',
            'status'        => 'nullable',
            'creator_id'    => 'nullable',
            'creator_type'  => 'nullable',
            'updater_id'    => 'nullable',
            'updater_type'  => 'nullable',
            'meta_data'     => 'nullable',
        ];
    }
}