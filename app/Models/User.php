<?php

namespace App\Models;

use App\Messages\ActivationMail;
use App\Tools;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    const TYPE_DISABLED = 0;
    const TYPE_CLIENT = 1;
    const TYPE_SUPPLIER = 2;
    const TYPE_SERVICE = 4;
    const TYPE_MANAGER = 8;

    protected $with = [
        'roles',
        'stats'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'permissions' => '[]',
        'preferences' => '[]',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string>
     */
    protected $casts = [
        'permissions' => 'json',
        'preferences' => 'json',
        'last_login' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'email',
        'type',
        'service_id',
        'creator_id',
        'responsable_id',
        'first_name',
        'last_name',
        'phone',
        'department',
        'permissions',
        'preferences',
        'remember_token',
    ];

    /**
     * The attributes append to JSON.
     *
     * @var array<string>
     */
    protected $appends = [
        'has_permissions',
        'tickets',
        'messages',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array<string>
     */
    protected $hidden = [
        'permissions',
        'password',
        'deleted_at',
        'remember_token',
    ];

    /**
     * Validation rules.
     *
     * @var array<string>
     */
    public static $rules = [
        'email' => 'required|email:rfc,dns',
        'type' => 'required',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
    ];

    /**
     * Relation after save.
     *
     * @var mixed
     */
    public static $rolesForSync = null;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        if (php_sapi_name() !== "cli") {
            static::saving(function ($user) {
                if (is_array($user->type)) {
                    $user->type = array_sum($user->type);
                }
                if (!$user->id && empty($user->creator_id)) {
                    $user->creator_id = Auth::user()->id;
                }
            });
            static::creating(function ($user) {
                if (Auth::user())
                    $user->service_id = Auth::user()->service_id;
                $user->remember_token = Str::random(8);
                $user->password = $user->remember_token;
            });
            static::created(function ($user) {
                try {
                    Mail::to($user)->send(new ActivationMail($user, Auth::user()));
                } catch (\Exception $exception) {
                    Tools::teamsAlert('Server Mail error: ' . $exception->getMessage());
                }
            });
            static::saved(function ($user) {
                if (static::$rolesForSync) {
                    $user->roles()->sync(static::$rolesForSync);
                    $user->load('roles');
                }
            });
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(self::class, 'creator_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function stats()
    {
        return $this->hasOne(UserStats::class);
    }

    /**
     * @return int
     */
    public function getTicketsAttribute()
    {
        return $this->hasMany(Ticket::class, 'creator_id')->count();
    }

    /**
     * @return int
     */
    public function getMessagesAttribute()
    {
        return $this->hasMany(Activity::class)->count();
    }

    /**
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * @return array<midex>
     */
    public function getHasPermissionsAttribute()
    {
        $roles = $this->roles;
        $this->roles->makeHidden('permissions');
        $permissions = $this->permissions;
        foreach ($roles as $role) {
            $permissions = array_merge($role->permissions, $permissions);
        }
        $permissions = array_merge($permissions, $this->permissions);

        return $permissions;
    }

    public function generateToken()
    {
        $persistence = Persistence::create(['user_id' => $this->id]);
        $this->remember_token = $persistence->id;
        return $this->remember_token;
    }
}
