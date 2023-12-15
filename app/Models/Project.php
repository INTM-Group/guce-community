<?php

namespace App\Models;

use App\Contracts\RelationalTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, RelationalTrait;

    /** @var bool $timestamps */
    public $timestamps = true;

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'participants' => '[]',
        'surveillance' => '[]',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'participants' => 'collection',
        'surveillance' => 'json',
        'budget_hours' => 'decimal:2',
        'budget_amount' => 'decimal:2',
        'budget_supplementary' => 'decimal:2',
        'cost' => 'decimal:2',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'status',
        'priority',
        'criticality',
        'service_id',
        'creator_id',
        'budget_hours',
        'budget_amount',
        'budget_supplementary',
        'cost',
        'title',
        'description',
        'risks',
        'participants',
        'surveillance'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
