<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function isSuperAdmin()
    {
        // Adjust this condition according to how you define superadmin in your database
        return $this->role === 'superadmin'; // Assuming you have a 'role' column
    }
    // Technicians created by this user
    public function createdTechnicians(): MorphMany
    {
        return $this->morphMany(Technician::class, 'creator');
    }

    // Technicians updated by this user
    public function updatedTechnicians(): MorphMany
    {
        return $this->morphMany(Technician::class, 'updater');
    }
    // Brands created by this user
    public function createdBrands(): MorphMany
    {
        return $this->morphMany(Brand::class, 'creator');
    }

    // Brands updated by this user
    public function updatedBrands(): MorphMany
    {
        return $this->morphMany(Brand::class, 'updater');
    }

     // Models created by this user
     public function createdModels(): MorphMany
     {
         return $this->morphMany(ProductModel::class, 'creator');
     }

     // Models updated by this user
     public function updatedModels(): MorphMany
     {
         return $this->morphMany(ProductModel::class, 'updater');
     }


    // Category created by this user
    public function createdCategorys(): MorphMany
    {
        return $this->morphMany(Category::class, 'creator');
    }

    // Categorys updated by this user
    public function updatedCategorys(): MorphMany
    {
        return $this->morphMany(Category::class, 'updater');
    }

    // Lines created by this user
    public function createdLines(): MorphMany
    {
        return $this->morphMany(Line::class, 'creator');
    }

    // Lines updated by this user
    public function updatedLines(): MorphMany
    {
        return $this->morphMany(Line::class, 'updater');
    }

     // Lines created by this user
     public function createdGroups(): MorphMany
     {
         return $this->morphMany(Line::class, 'creator');
     }

     // Lines updated by this user
     public function updatedGroups(): MorphMany
     {
         return $this->morphMany(Line::class, 'updater');
     }

}
