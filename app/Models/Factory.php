<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Factory extends Model
{
    use HasFactory;

    protected $fillable = ['uuid','company_id', 'name', 'factory_code', 'email', 'phone', 'location', 'status'];

    public function floors(): BelongsToMany
    {
        return $this->belongsToMany(Floor::class);
    }
    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }

    // public function lines()
    // {
    //     return $this->belongsToMany(Line::class);
    // }
    public function user()
    {
        return $this->belongsTo(User::class, 'company_id'); // Adjust 'company_id' if the foreign key is different
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
