<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;

use App\Job;
use App\Ticket;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets created.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('ticket');

        $limit = $request->input('limit') ? : 100;
        $job_id = trim($request->input('job_id'));
        $imei = trim($request->input('imei'));

        $tickets = Ticket::where('flag', true)
                                ->whereHas('job', function ($query) use($job_id, $imei) {
                                                if ($job_id) {
                                                    $query->where('id', $job_id);
                                                }

                                                if ($imei) {
                                                    $query->where('imei', $imei);
                                                }
                                            })
                                ->orderBy('id', 'desc')
                                ->paginate($limit);

        return view('tickets.index', compact('tickets'));
    }


    /**
     * Show the form for creating a new ticket.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('ticket');

        return view('tickets.create');
    }


    /**
     * Store a newly created ticket in DB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('ticket');

        $today = Carbon::now('Asia/Manila');

        Ticket::create([
                        'job_id' => $request->job_id, 
                        'company_id' => Auth::user()->company_id, 
                        'type' => $request->type, 
                        'customer_name' => $request->customer_name, 
                        'customer_contact' => $request->customer_contact, 
                        'description' => $request->description,
                        'created_by' => $request->user()->id,
                        ]);

        flash(trans('cdu.create_ticket_success', ['job' => sprintf('JO%08d', $request->job_id)]), 'success');

        return redirect()->route('ticket.index');
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
}
