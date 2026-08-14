<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use App\Http\Controllers\Controller;

use App\Customer;
use Auth;

class CustomerController extends Controller
{
    /**
     * Display registered customer list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('crm');

        $limit = $request->input('limit') ? : 50;
        $name = trim($request->input('name')) ? : null; 
        $email = trim($request->input('email')) ? : null; 

        $customers = Customer::with('state', 'creator')
                    ->where('flag', true)
                    ->where(function($query) use($name, $email) {
                                                                    if ($name) {
                                                                        $query->where('name', 'like', '%'.$name.'%');
                                                                    }
                                                                    
                                                                    if ($email) {
                                                                        $query->where('email', 'like', '%'.$email.'%');
                                                                    }
                                                                })
                    ->paginate($limit);

        //dd($customers);

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('crm');

        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $this->authorize('crm');

        $request['created_by'] = $request->user()->id;
        Customer::create($request->all());

        flash(trans('validation.create_success', ['attribute' => 'customer']), 'success');

        return redirect()->route('customer.index');
    }

    /**
     * Show the form for editing customer's details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('crm');

        $customer = Customer::find($id);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update & store updated customer's details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $this->authorize('crm');

        $customer = Customer::findOrFail($id);
        
        Customer::find($id)->update([
                                'name' => $request->get('name'),
                                'email' => $request->get('email'),
                                'gender' => $request->get('gender'),
                                'dob' => $request->get('dob'),
                                'id_type' => $request->get('id_type'),
                                'id_number' => $request->get('id_number'),
                                'mobile_number' => $request->get('mobile_number'),
                                'home_number' => $request->get('home_number'),
                                'fax_number' => $request->get('fax_number'),
                                'address' => $request->get('address'),
                                'postcode' => $request->get('postcode'),
                                'state_id' => $request->get('state_id'),
                                'country_id' => $request->get('country_id'),
                                'updated_by' => Auth::id()
                            ]);

        flash(trans('validation.update_success', ['attribute' => 'customer']), 'success');

        return redirect()->route('customer.index');
    }

    /**
     * Deactivate customer's account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('crm');

        Customer::find($id)->update([
                                'flag' => false,
                                'updated_by' => Auth::id()
                            ]);

        flash(trans('validation.delete_success', ['attribute' => 'customer']), 'success');

        return redirect()->route('customer.index');
    }
}
