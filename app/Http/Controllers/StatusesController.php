<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Status;
use App\Event;

class StatusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//    Tell StatusesController which middleware to use
    public function __construct()
    {
        $this->middleware('auth.basic');
    }

//    Allow admin to view statuses of all invitations
    public function index()
    {
        //
        $this->authorize('viewAny',Status::class);
        return response()->json(Status::all());
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

//    Show all the invitation statuses of a particular event
    public function showStatusesOfEventInvitation($event_id)
    {
        $statuses = Status::all()->where('event_id',$event_id);
        return response()->json($statuses);
    }
}
