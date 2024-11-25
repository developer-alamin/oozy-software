<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'uuid',
        'machine_id',
        'line_id',
        'description',
        'status',
    ];
    public function machine()
    {
        return $this->belongsTo(MechineAssing::class, 'machine_id');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id');
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
