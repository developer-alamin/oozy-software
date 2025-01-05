<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreventiveServiceDetail extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "preventive_service_details"; 
    protected $fillable = [
        'uuid',
        'status',
        'technician_status',
        'preventive_service_id',
        'helper_technician_id',
        'action_id',
        'creator_type',
        'creator_id ',
        'updater_type',
        'updater_id',
    ];   
    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }

    public function preventiveService(){
        return $this->belongsTo(PreventiveService::class);
    }

    public function user(){
        return $this->belongsTo(User::class,"technician_id",'id');

    }
}
