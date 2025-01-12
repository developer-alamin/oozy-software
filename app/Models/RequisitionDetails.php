<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequisitionDetails extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "requisition_details";
    protected $fillable = [
        "uuid",
        "mc",
        "requisition_id",
        "machine_type_id",
        "creator_type",
        "creator_id",
        "updater_type",
        "updater_id"
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }

    public function machineType()
    {
        return $this->belongsTo(MechineType::class);
    }
    

}
