<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use Illuminate\Http\Request;
use App\Event;
use App\User;
use App\Status;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//    Tell EventssController which middleware to use
    public function __construct()
    {
        $this->middleware('auth.basic');
    }

//    Allow admin to view all events' details
    public function index()
    {
        //
        $this->authorize('viewAny',Event::class);
        $events = Event::all();
        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

//    Allow new users to create event
    public function store(Request $request)
    {
        //
        $event = new Event();
        $user_id = auth()->id();
        $attributes = $request->validate([
            'name'=>'required',
            'start'=>'required',
            'end'=>'required'
        ]);
        $attributes['owner_id'] = $user_id;
        $event->create($attributes);
        $message = 'You have created an event';
//        Send email to user confirming creation of event
        $user = User::find($user_id);
        \Mail::to($user)->send(new Email($message));
//
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//    Show a particular event's details
    public function show($id)
    {
        //
        $event = Event::find($id);
        $this->authorize('view',$event);
        return response()->json(Event::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//    Allow owner of event to update event details
    public function update(Request $request, $id)
    {
        //
        $event = Event::find($id);
        $this->authorize('update',$event);
        $event->update($request->validate([
            'owner_id' =>'required',
            'name' =>'required',
            'start' =>'required',
            'end' => 'required'
        ]));
//        Send email to owner confirming updation of event details
        $message = 'You have updated an event';
        \Mail::to($attributes['email'])->send(new Email($message));
//
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//    Allow owner or admin to delete event (Should be used only if no one has been invited yet)
    public function cancel($id)
    {
        //
        $event = Event::findOrFail($id);
        $this->authorize('delete',$event);
        $users = $event->users;
        foreach($users as $user)
        {
            $user->pivot->status = 3;
            $user->pivot->save();
        }
        $event->delete();
//        Send email to user that they have deleted event
        $message = 'You have deleted an event';
        $user = User::findOrFail(auth()->id());
        \Mail::to($user)->send(new Email($message));
    }

//    Allow event owner to cancel event and then notify all invitees and invitation statuses
    public function destroy($id)
    {
//        $id = $request->id;
        $event = Event::findOrFail($id);
        $this->authorize('update',$event);
        $statuses = Status::all()->where('event_id',$event->id);
        foreach($statuses as $status)
        {
            $status->status = 3;
            $status->save();
            $user = User::find($status->user_id);
//            Send email to invitees informing of cancellation of event
            $message = 'Your event has been cancelled';
            \Mail::to($user)->send(new Email($message));
        }
    }

//    Allow admin or owner of event to view event invitees
    public function eventusers($event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('view',$event);
        $users = $event->users;
        return response()->json($users);
    }

//    Allow event owner to invite other users to event
    public function invite(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $this->authorize('update',$event);
        $event->users()->sync($request->get('list'));
        $invited_users_id  = $request->get('list');
//        Send email invitation to all users
        $message = 'You have been invited';
        foreach($invited_users_id as $user_id)
        {
            $user = User::findOrFail($user_id);
            \Mail::to($user)->send(new Email($message));
        }

    }


}
