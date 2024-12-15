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
        'uuid',
        'machine_id',
        'technician_id',
        'line_id',
        'location',
        'breakdown_problem_note_id',
        'breakdown_problem_note',
        'breakdown_service_status',
        'breakdown_service_technician_status',
        'service_time',
        'service_date',
    ];

    public static function validationRules()
    {
        return [
            'machine_id'                           => 'required',
            'technician_id'                        => 'nullable',
            'line_id'                              => 'required',
            'location'                             => 'required',
            'breakdown_problem_note_id'            => "nullable",
            'breakdown_problem_note'               => "nullable",
            'breakdown_service_status'             => 'nullable',
            'breakdown_service_technician_status'  => 'nullable',
            'service_time'                         => 'nullable',
            'service_date'                         => 'nullable',
            'creator_id'                           => 'nullable',
            'creator_type'                         => 'nullable',
            'updater_id'                           => 'nullable',
            'updater_type'                         => 'nullable',
        ];
    }
    public function line()
    {
        return $this->belongsTo(Line::class, 'line_id');
    }
    public function technician()
    {
        return $this->belongsTo(Technician::class, 'technician_id');
    }
    public function mechineAssing()
    {
        return $this->belongsTo(MechineAssing::class, 'machine_id');
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