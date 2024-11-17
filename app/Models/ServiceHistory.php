<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceHistory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'service_id',
        'operator_id',
        'operator_mechine_problem_note',
        'operator_cell_time',
        'technician_id',
        'technician_note',
        'technician_arrive_time',
        'technician_working_time',
        'technician_status',
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
