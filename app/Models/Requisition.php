<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requisition extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "requisitions";
    protected $fillable = [
        "uuid",
        "startDate",
        "endDate",
        "style",
        "total",
        "company_id",
        "line_id",
        "creator_type",
        "creator_id",
        "updater_type",
        "updater_id"
    ];


    public function requisition_details(){
        return $this->hasMany(Requisition::class,"requisition_id","id");
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function requisitionDetails()
    {
        return $this->hasMany(RequisitionDetails::class);
    }
}
