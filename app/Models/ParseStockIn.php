<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParseStockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'parse_id',
        'quantity_in',
        'type',
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