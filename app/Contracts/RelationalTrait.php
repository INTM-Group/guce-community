<?php

namespace App\Contracts;

use App\Models\Activity;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\QueryException;

/**
 * Trait RESTActions
 *
 * @package App\Contracts
 */
trait RelationalTrait
{
    /**
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * The users that belong to the role.
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The users that belong to the role.
     */
    public function getParticipantsUsersAttribute()
    {
        try {
            return $this->hasMany(User::class, 'id', 'participants')->get();
        } catch (QueryException $e) {
            return $this->participants;
        }
    }

    /**
     * The users that belong to the role.
     */
    public function addParticipants(User $user)
    {
        $this->participants = $this->participants->push($user->id)->unique();
        return $this;
    }

    /**
     * The users that belong to the role.
     */
    public function activities()
    {
        return $this->morphMany(Activity::class, 'target');
    }
}
