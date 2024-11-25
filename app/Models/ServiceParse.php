<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceParse extends Model
{
    use HasFactory;
    protected $fillable = [
        'service_id',
        'parse_id',
        'use_qty',
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
