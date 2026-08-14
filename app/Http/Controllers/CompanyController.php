<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CompanyRequest;
use App\Http\Controllers\Controller;

use App\Company;
use Auth;
use Gate;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('company_mgmt');

        /*if( Gate::denies('show-company') ) {
            abort(403, 'You don\'t have the access to this page.');
        }*/

        $limit = $request->input('limit') ? : 50;
        $companies = Company::with('state', 'creator')->where('flag', true)->paginate($limit);

        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('company_mgmt');
        
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CompanyRequest $request)
    {
        $this->authorize('company_mgmt');
        
        $request['created_by'] = $request->user()->id;
        Company::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'company']), 'success');

        return redirect()->route('company.index');
        //return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('company_mgmt');
        
        $company = Company::where('id', $id)->where('status', 1)->first();

        if( !$company ) {
            //flash()->error('Page Not Found', 'The URL you are accessing no longer available.');
            //return \Redirect::back();
            return view('errors.404');
        }
        
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('company_mgmt');
        
        $company = Company::find($id);

        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id)
    {
        $this->authorize('company_mgmt');
        
        $company = Company::findOrFail($id);

        Company::find($id)->update([
            'company_name' => $request->get('company_name'),
            'company_type' => $request->get('company_type'),
            'company_prefix' => $request->get('company_prefix'),
            'email' => $request->get('email'),
            'contact_number' => $request->get('contact_number'),
            'fax_number' => $request->get('fax_number'),
            'address' => $request->get('address'),
            'state_id' => $request->get('state_id'),
            'updated_by' => Auth::id()
            ]);

        flash(trans('validation.update_success', ['attribute' => 'company']), 'success');

        return redirect()->route('company.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('company_mgmt');
        
        Company::find($id)->update([
                                    'flag' => false,
                                    'updated_by' => Auth::id()
                                    ]);

        flash(trans('validation.delete_success', ['attribute' => 'company']), 'success');

        return redirect()->route('company.index');
    }
}
