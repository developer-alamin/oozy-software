<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreventiveService extends Model
{
    use HasFactory,SoftDeletes;

    public function creator()
    {
        return $this->morphTo();
    }

    public function updater()
    {
        return $this->morphTo();
    }
    public function company()
    {
        return $this->belongsTo(Company::class, );
    }
    public function mechine_assing()
    {
        return $this->belongsTo(MechineAssing::class, 'mechine_assing_id');
    }
    // Define the relationship
    public function serviceDetails()
    {
        return $this->hasMany(PreventiveServiceDetail::class, 'preventive_service_id', 'id'); // 'prevent_service_id' is the foreign key, 'id' is the local key
    }

}
