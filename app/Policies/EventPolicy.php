<?php

namespace App\Policies;

use App\User;
use App\Event;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any events.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
//        echo $user->id;
        return $user->id == 1;
    }

    /**
     * Determine whether the user can view the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function view(User $user, Event $event)
    {
        //
//        dd($user->id);
        return $event->owner_id == $user->id or $user->id == 1;
    }

    /**
     * Determine whether the user can create events.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
//        dd("hi");
        return true;
    }

    /**
     * Determine whether the user can update the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function update(User $user, Event $event)
    {
        //
        return $event->owner_id == $user->id;
    }

    /**
     * Determine whether the user can delete the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function delete(User $user, Event $event)
    {
        //
        return ($event->owner_id == $user->id or $user->id == 1);
    }

    /**
     * Determine whether the user can restore the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function restore(User $user, Event $event)
    {
        //
        return $event->owner_id == $user->id;
    }

    /**
     * Determine whether the user can permanently delete the event.
     *
     * @param  \App\User  $user
     * @param  \App\Event  $event
     * @return mixed
     */
    public function forceDelete(User $user, Event $event)
    {
        //
        return $event->owner_id == $user->id;
    }
}
