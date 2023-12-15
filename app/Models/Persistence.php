<?php

namespace App\Models;

use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;
use Ramsey\Uuid\Type\Integer;
use Ramsey\Uuid\Uuid;

class Persistence extends Model
{

    /** @var bool $incrementing */
    public $incrementing = false;

    /** @var string $keyType */
    protected $keyType = 'string';

    /** @var bool $timestamps */
    public $timestamps = true;

    /**
     * The model's default values for attributes.
     *
     * @var array<mixed>
     */
    protected $attributes = [
        'connected' => 1,
    ];

    protected $with = [
        'user'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ['user_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = ['user_id'];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $domine = Uuid::DCE_DOMAIN_PERSON;
            $userId = new Integer($model->user_id);
            $node = new RandomNodeProvider();
            $model->id = AuthServiceProvider::uuidFactory()
                ->uuid2($domine, $userId, $node->getNode())
                ->toString();
        });
    }

    /**
     * The user that belong.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
