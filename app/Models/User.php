<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'phone',
        'status',
        'creator_type',
        'creator_id ',
        'updater_type',
        'updater_id '
    ];

    protected $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
        'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
    ];

    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:users,email',
            'photo'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Example for image validation
            'password'     => 'required|string|min:8|max:255',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }




    public function createdTechnicians()
    {
        return $this->morphMany(Technician::class, 'creator');
    }

    // Technicians updated by this user
    public function updatedTechnicians()
    {
        return $this->morphMany(Technician::class, 'updater');
    }

    // Brands created by this user
    public function createdBrands()
    {
        return $this->morphMany(Brand::class, 'creator');
    }

    // Brands updated by this user
    public function updatedBrands()
    {
        return $this->morphMany(Brand::class, 'updater');
    }

    // Models created by this user
    public function createdModels()
    {
        return $this->morphMany(ProductModel::class, 'creator');
    }

    // Models updated by this user
    public function updatedModels()
    {
        return $this->morphMany(ProductModel::class, 'updater');
    }

    // Category created by this user
    public function createdCategorys()
    {
        return $this->morphMany(Category::class, 'creator');
    }

    // Categorys updated by this user
    public function updatedCategorys()
    {
        return $this->morphMany(Category::class, 'updater');
    }
    // Units created by this user
    public function createdUnits()
    {
        return $this->morphMany(Unit::class, 'creator');
    }

    // Units updated by this user
    public function updatedUnits()
    {
        return $this->morphMany(Unit::class, 'updater');
    }
     // Parse Units created by this user
    public function createdParseUnits()
    {
        return $this->morphMany(Unit::class, 'creator');
    }
    // Parse Units updated by this user
    public function updatedParseUnits()
    {
        return $this->morphMany(Unit::class, 'updater');
    }
     // Lines created by this user
    public function createdLines()
    {
        return $this->morphMany(Line::class, 'creator');
    }

    // Lines updated by this user
    public function updatedLines()
    {
        return $this->morphMany(Line::class, 'updater');
    }
     // groups created by this user
     public function createdGroups()
     {
         return $this->morphMany(Group::class, 'creator');
     }

     // groups updated by this user
     public function updatedGroups()
     {
         return $this->morphMany(Group::class, 'updater');
     }

      // factorys created by this user
    public function createdFactorys()
    {
        return $this->morphMany(Factory::class, 'creator');
    }

    // factorys updated by this user
    public function updatedFactorys()
    {
        return $this->morphMany(Factory::class, 'updater');
    }

    public function factories(){
        return $this->hasMany(Factory::class,);
    }
     // Operators created by this user
     public function createdOperators()
     {
         return $this->morphMany(Operator::class, 'creator');
     }

     // Operators updated by this user
     public function updatedOperators()
     {
         return $this->morphMany(Operator::class, 'updater');
     }
    // mechine assing created by this user
    public function createdMechineAssings()
    {
        return $this->morphMany(MechineAssing::class, 'creator');
    }
    // mechine assing updated by this user
    public function updatedMechineAssings()
    {
        return $this->morphMany(MechineAssing::class, 'updater');
    }

    //Parse created by this user
    public function createdParses()
    {
        return $this->morphMany(Parse::class, 'creator');
    }
    //  mechine assing updated by this user
    public function updatedParses()
    {
        return $this->morphMany(Parse::class, 'updater');
    }

    public function createdParseInStocks()
    {
        return $this->morphMany(ParseStockIn::class, 'creator');
    }
    //  mechine assing updated by this user
    public function updatedParseInStocks()
    {
        return $this->morphMany(ParseStockIn::class, 'updater');
    }

    // mechine Stock created by this user
    public function createdMechineStocks()
    {
        return $this->morphMany(MechineStock::class, 'creator');
    }
    //  mechine Stock updated by this user
    public function updatedMechineStocks()
    {
        return $this->morphMany(MechineStock::class, 'updater');
    }

    // mechine Stock created by this user
    public function createdServices()
    {
        return $this->morphMany(Service::class, 'creator');
    }
    //  mechine Stock updated by this user
    public function updatedServices()
    {
        return $this->morphMany(Service::class, 'updater');
    }
    public function createdMovement()
    {
        return $this->morphMany(Movement::class, 'creator');
    }
    //  movement 
    public function updatedMovement()
    {
        return $this->morphMany(Movement::class, 'updater');
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
