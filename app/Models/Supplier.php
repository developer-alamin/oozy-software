<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;
    
    // Specify the fillable attributes
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'contact_person',
        'address',
        'description',
        'photo', // For the uploaded photo
        'type',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id'

    ];
    protected $casts = [
        'uuid'       => 'string',
        'id'         => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];
    // Specify the hidden attributes (optional)
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Optionally, you can define any relationships here
    // For example, if the Supplier model has many Products:

    // Optionally, you can define validation rules for creating/updating a Supplier
    public static function rules()
    {   
       

        return [
            'company_id'   => 'required|exists:companies,id',
            'name'           => 'required|string|max:255',
            'type'           => 'nullable',
            'email'          => 'required|email|max:255|unique:suppliers,email',
            'phone'          => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:500',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Example for image validation
        ];
    }

    // Polymorphic relationships
    public function creator()
    {
        return $this->morphTo();
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function updater()
    {
        return $this->morphTo();
    }
}