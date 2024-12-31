<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MachineTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'status',
        'note',
        'company_id',
        'creator_type',
        'creator_id',
        'updater_type',
        'updater_id',
    ];

    protected $casts = [
        'uuid'       => 'string',
        'id'         => 'integer',
        'company_id' => 'integer',
        'creator_id' => 'integer',
        'updater_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Validation rules for MachineTag model.
     *
     * @return array
     */
    public static function validationRules()
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
            'status'     => 'nullable|in:Active,Inactive',
            'note'       => 'nullable|string',
        ];
    }
    /**
     * Relationship: Get the company associated with the MachineTag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relationship: Get the creator of the MachineTag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function creator()
    {
        return $this->morphTo();
    }

    /**
     * Relationship: Get the updater of the MachineTag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function updater()
    {
        return $this->morphTo();
    }
}
