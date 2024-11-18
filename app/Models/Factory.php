<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Factory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'uuid',
        'company_id',
        'name',
        'factory_code',
        'factory_owner',
        'factory_size',
        'factory_capacity',
        'email',
        'phone',
        'location',
        'status',
        'note',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
    public function floors()
    {
        return $this->hasMany(Floor::class);
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