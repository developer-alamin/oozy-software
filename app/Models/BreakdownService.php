<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownService extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "breakdown_services";
    protected $fillable = [
        "uuid", "date_time", "service_status",
        "supervisor_problem_note_id", "supervisor_note",
        "company_id", "mechine_assing_id", "technician_id",
        "creator_type", "creator_id", "updater_type", "updater_id"
    ];
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }

    public function mechine_assing()
    {
        return $this->belongsTo(MechineAssing::class, 'mechine_assing_id');
    }

    public function service_details(){
        return $this->hasMany(BreakdownServiceDetail::class);
    }
    public function deatail()
    {
        return $this->belongsTo(BreakdownServiceDetail::class, 'breakdown_service_id');
    }
    

}
