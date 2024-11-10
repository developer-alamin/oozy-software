<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechineStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'mechine_assing_id',
        'quantity',
        'type',
        'status',
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