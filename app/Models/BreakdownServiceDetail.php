<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreakdownServiceDetail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "breakdown_service_details";
    protected $fillable = [
        "uuid",
        "status",
        "technician_status",
        "acknowledge_date_time",
        "service_start_date_time",
        "service_end_date_time",
        "problem_note_id",
        "helper_technician_id",
        "action_id",
        "note",
        "parts_info",
        "breakdown_service_id",
        "technician_id",
        "creator_type",
        "creator_id",
        "updater_type ",
        "updater_id "
    ];
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }

    public function breckdown_service()  {
        return $this->belongsTo(BreakdownService::class,"breakdown_service_id","id");
    }
}
