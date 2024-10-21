<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['name','description', 'status','meta_data'];
    
    public static function validationRules()
    {
        return [
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'nullable',
            'meta_data'    => 'nullable',
        ];
    }

    public static function restoreBrand($id)
    {
        // Find the brand by id in the trashed records
        $brand = self::onlyTrashed()->find($id);

        // If no brand is found, return false
        if (!$brand) {
            return false;
        }

        // Restore the brand
        $brand->restore();

        return true;
    }

}
