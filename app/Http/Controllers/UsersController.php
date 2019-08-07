<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\User;
use App\Event;
use App\Status;
use App\Mail\Email;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//    Tell UsersController which middleware to use
    public function __construct()
    {
        $this->middleware('auth.basic');
    }

//    Allow admin to view all users' details
    public function index()
    {
        $this->authorize('viewAny',User::class);
        $users = User::all();
        return response()->json($users);
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

//    Allow new users to create account
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' =>'required',
            'email' =>'required',
            'password'=>'required'
        ]);
        $attributes['password'] = bcrypt($attributes['password']);
        try
        {
            User::create($attributes);
        }
        catch(QueryException $exception)
        {
            $errorInfo = $exception->errorInfo;
            if($errorInfo[0] == "23000")
            {
                $error_message = "A user with the same email id already exists";
            }
            return response()->json($error_message);
        }
        $message = 'Your user account has been created';
        \Mail::to($attributes['email'])->send(new Email($message));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//    Show particular user's details
    public function show($id)
    {
        //
        $user = User::find($id);
        $this->authorize('view',$user);
        return response()->json(User::find($id));
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

//    Allow users to update their own credentials
    public function update(Request $request, $id)
    {
        //
        $this->authorize('update',$user);
//        $user = User::find($id);
        $attributes = $request->validate([
            'name' =>'required',
            'email' =>'required',
            'password'=>'required'
        ]);
        $attributes['password'] = bcrypt($attributes['password']);
        try
        {
            User::update($attributes);
        }
        catch(QueryException $exception)
        {
            $errorInfo = $exception->errorInfo;
            if($errorInfo[0] == "23000")
            {
                $error_message = "A user with the same email id already exists";
            }
            return response()->json($error_message);
        }
//          Send email to the user who updated his credentials
        $message = 'You have updated your information';
        \Mail::to($attributes['email'])->send(new Email($message));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//    Allow user or admin to delete their account
    public function destroy($id)
    {
        //
        $user = User::find($id);
        $this->authorize('delete',$user);
        $statuses = Status::all();
//        Delete all invitations for the user
        foreach($statuses as $status)
        {
            if($status->user_id == $id)
            {
                $status->delete();
            }
        }
        $user->delete();
//        Send email to the user whose account ha been deleted
        $message = 'Your account has been deleted';
        \Mail::to($user)->send(new Email($message));
    }

//    View all the events a particular user has been invited to
    public function viewEventsOfUser()
    {
        $user_id = auth()->id();
        $user = User::findOrFail($user_id);
        $events = $user->events;
        return response()->json($events);
    }

//    Allow user to accept an invitation
//    public function accept($id)
//    {
//        $event = Event::findorFail($id);
//        $user_id = auth()->id();
//        echo $user_id;
//        $user = $event->users()->where('user_id',$user_id)->get()->first();
//        if(count($user))
//        {
//            $user->pivot->status = 1;
//            $user->pivot->save();
////            Send mail to user confirming his acceptance
//            $message = 'You have accepted the invitaiton';
//            \Mail::to($user)->send(new Email($message));
//        }
//        else
//        {
//            return response("Not invited to event",403);
//        }
//    }

//    Allow user to reject an invitation
//    public function reject($id)
//    {
//        $event = Event::findOrFail($id);
//        $user_id = auth()->id();
//        $user = $event->users()->where('user_id',$user_id)->get()->first();
//        if(count($user))
//        {
//            $user->pivot->status = 2;
//            $user->pivot->save();
////            Send email to user confirming his rejection
//            $message = 'You have rejected the invitation';
//            \Mail::to($user)->send(new Email($message));
//        }
//        else
//        {
//            return response("Not invited to event",403);
//        }
//    }

    public function respondToInvitation(Request $request)
    {
        $id = $request->event_id;
        $status = $request->status;
        if($status != 1 and $status != 2)
        {
            return response()->json("Not valid status code", 403);
        }
        $event = Event::findOrFail($id);
        $user_id = auth()->id();
        $user = $event->users()->where('user_id',$user_id)->get()->first();
        if(count($user))
        {
            $user->pivot->status = $status;
            $user->pivot->save();
//            Send email to user confirming his rejection
            $message = 'You have rejected the invitation';
            \Mail::to($user)->send(new Email($message));
        }
        else
        {
            return response("Not invited to event",403);
        }
    }

}
