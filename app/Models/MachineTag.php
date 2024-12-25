<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineTag extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
      'uuid',
      'name',
      'status',
      'note',
    ];
  protected $casts = [
      'unit_id'    => 'integer', // Casts factory_id to an integer
      'uuid'       => 'string',
      'id'         => 'integer',
      'creator_id' => 'integer',
      'updater_id' => 'integer',
      'created_at' => 'datetime', // Automatically cast 'created_at' to a Carbon instance
      'updated_at' => 'datetime', // Automatically cast 'updated_at' to a Carbon instance
  ];

  public static function validationRules()
  {
      return [
          'name'          => 'required|string|max:255',
          'status'        => 'nullable|string',
          'note'          => 'nullable|string',
      ];
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