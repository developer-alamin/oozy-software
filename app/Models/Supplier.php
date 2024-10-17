<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional)
    protected $table = 'suppliers'; // Only necessary if the table name is not the plural form of the model

    // Specify the fillable attributes
    protected $fillable = [
        'name',
        'email',
        'phone',
        'contact_person',
        'address',
        'description',
        'photo', // For the uploaded photo
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Example for image validation
        ];
    }
}
