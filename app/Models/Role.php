<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    const TYPE_DISABLED = 0;
    const TYPE_CLIENT = 1;
    const TYPE_SUPPLIER = 2;
    const TYPE_SERVICE = 4;
    const TYPE_MANAGER = 8;

    /** @var bool $timestamps */
    public $timestamps = true;

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'permissions' => '[]',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'permissions' => 'json',
        'description' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'slug',
        'type',
        'description',
        'permissions',
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
     * The users that belong to the role.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
