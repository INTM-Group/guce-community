<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    const TYPE_UNKNOWN = 0;
    const TYPE_UPDATE = 1;
    const TYPE_STATUS = 2;
    const TYPE_MESSAGE = 4;
    const TYPE_CLIENT = 8;
    const TYPE_16 = 16;
    const TYPE_32 = 32;
    const TYPE_64 = 64;
    const TYPE_OTHER = 128;

    /** @var bool $timestamps */
    public $timestamps = true;

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'settings' => '{}',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'settings' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'settings',
        'name'
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
     * Validation rules.
     *
     * @var array<string>
     */
    public static $rules = [
        'settings' => 'required',
    ];

    /**
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * The users that belong to the role.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * The users that belong to the role.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
