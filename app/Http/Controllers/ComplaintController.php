<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ComplaintRequest;
use App\Http\Controllers\Controller;

use App\Complaint;
use Auth;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('complaint_mgmt');
        
        $limit = $request->input('limit') ? : 15;
        $complaints = Complaint::with('creator')->where('flag', true)
                                                ->orderBy('parent_id', 'asc')
                                                ->orderBy('code', 'asc')
                                                ->paginate($limit);
        $complaint_category = Complaint::where('parent_id', 0)->lists('name', 'id')->all();

        return view('complaints.index', compact('complaints', 'complaint_category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('complaint_mgmt');

        $complaint_category = Complaint::where('parent_id', 0)->where('flag', true)->lists('name', 'id')->all();

        return view('complaints.create', compact('complaint_category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ComplaintRequest $request)
    {        
        $this->authorize('complaint_mgmt');

        $request['created_by'] = $request->user()->id;
        $request['flag'] = true;
        Complaint::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'complaint']), 'success');

        return redirect()->route('complaint.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('complaint_mgmt');
        
        $complaint = Complaint::find($id);
        $complaint_category = Complaint::where('parent_id', 0)->where('flag', true)->lists('name', 'id')->all();

        return view('complaints.edit', compact('complaint', 'complaint_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ComplaintRequest $request, $id)
    {
        $this->authorize('complaint_mgmt');
        
        Complaint::findOrFail($id)->update([
                                            'name' => $request->get('name'),
                                            'code' => $request->get('code'),
                                            'parent_id' => $request->get('parent_id'),
                                            'updated_by' => Auth::id()
                                            ]);

        flash(trans('validation.update_success', ['attribute' => 'complaint']), 'success');

        return redirect()->route('complaint.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('complaint_mgmt');

        $childComplaint = Complaint::where('parent_id', $id)->where('flag', true)->count();

        if($childComplaint > 0) {
            flash(trans('validation.delete_failure', ['attribute' => 'Please remove all existing sub-complaint under this complaint.']), 
                        'warning');
        } else {
            Complaint::findOrFail($id)->update([
                'flag' => false,
                'updated_by' => Auth::id()
            ]);

            flash(trans('validation.delete_success', ['attribute' => 'complaint']), 'success');
        }

        return redirect()->route('complaint.index');
    }
}
