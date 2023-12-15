<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Activity extends Model
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
        'data' => '[]',
        'message' => '',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'data' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type',
        'user_id',
        'target_type',
        'target_id',
        'data',
        'message',
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
        'data' => 'required',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        if (php_sapi_name() !== "cli") {
            static::creating(function ($activity) {
                $user = Auth::user();
                $activity->user_id = $user->id;
            });
        }
    }

    /**
     * The users that belong to the role.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that belong to the role.
     */
    public function target()
    {
        return $this->morphTo();
    }
}
