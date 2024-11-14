<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'mechine_id',
        'service_time',
        'service_date',
        'service_type_status',
    ];

    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
}
