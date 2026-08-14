<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Hash;

use App\User;
use App\Company;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('log', ['only' => ['fooAction', 'barAction']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('user_mgmt');

        //Add company for search as requested by Alvin Dela Cruz Feb. 18, 2019 c/o Mary Anne Garalde
        $company_id = trim($request->input('company_id'));
        $limit = $request->input('limit') ? : 15;
        $name = trim($request->input('name')) ? : null; 
        $users = User::with('role', 'company', 'creator')
                    ->where('id', '<>', 1)
                    ->where(function($query) use($name,$company_id) {
                                                                    if ($name) {
                                                                        $query->where('name', 'like', '%'.$name.'%');
                                                                    }
                                                                    if ($company_id) {
                                                                        $query->where('company_id','=', $company_id);
                                                                    }
                                                                })
                    ->paginate($limit);
        $companies = Company::where('flag', true)->orderBy('company_name', 'ASC')->lists('company_name', 'id');
        return view('users.index', compact('users','companies'));
    }

    /**
     * Show the form for editing system user's info.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('user_mgmt');

        $user = User::where(function ($query) use($id) {
                                $query->where('id', '<>', 1);
                                $query->where('id', $id);
                            })
                    ->firstOrFail();

        return view('users.edit', compact('user'));
    }

    /**
     * Update the system user's info and insert into DB.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('user_mgmt');

        $this->validate($request, [
            'name' => 'required|max:45',
            'email' => 'required|email|max:45|unique:users,email,' . $id . ',id,flag,1',
            'role_id' => 'required|integer|exists:roles,id',
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        
        User::findOrFail($id)->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'role_id' => $request['role_id'],
            'company_id' => $request['company_id'],
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'User']), 'success');

        return redirect()->route('user.index');
    }

    /**
     * Show existing login user profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProfile()
    {
        $user = Auth::user();
        //dd($user->id);

        return view('users.profile', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $this->validate($request, [
            'name' => 'required|max:45',
            'email' => 'required|email|max:45|unique:users,email,'.$user->id
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->updated_by = $user->id;
        
        $user->save();

        return back();
    }

    /**
     * Show password reset form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPassword()
    {
        return view('users.password');
    }

    /**
     * Update user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        // dd($request->password);

        $this->validate($request, [
            'password' => 'required|confirmed|min:6|max:20'
        ]);

        $user_password = Auth::user()->password;

        if (Hash::check($request->old_password, $user_password)) {
            
            Auth::user()->update([
                'password' => bcrypt($request->password),
                'updated_by' => Auth::id()
            ]);

            Auth::logout();
            return redirect()->route('home');
        } else {
            flash(trans('validation.update_fail', ['attribute' => 'password']), 'danger');
            return redirect()->route('password.edit');
        }
    }


    /**
     * Show password reset form.
     *
     * @return \Illuminate\Http\Response
     */
    public function editPasswordReset($id)
    {
        $user = User::find($id);

        return view('users.resetPassword', compact('user'));
    }

    /**
     * Update user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePasswordReset(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:users,id',
            'password' => 'required|confirmed|min:6|max:20'
        ]);

        $user = User::find($request->id);

        $user->update([
            'password' => bcrypt($request->password),
            'updated_by' => Auth::id()
        ]);

        flash(trans('validation.update_success', ['attribute' => 'user\'s password']), 'success');

        return redirect()->route('user.index');
    }

    /**
     * Activate user account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activateUser($id)
    {
        $user = User::find($id);

        $user->update([
            'flag' => 1
        ]);

        flash(trans('cdu.user_activation_status', ['user' => $user->name, 'status' => 'activate']), 'success');

        return redirect()->route('user.index');
    }

    /**
     * Deactivate user account.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivateUser($id)
    {
        $curr_user_id = Auth::user()->id;
        $user = User::find($id);

        $user->update([
            'flag' => 0
        ]);

        flash(trans('cdu.user_activation_status', ['user' => $user->name, 'status' => 'deactivate']), 'success');

        return redirect()->route('user.index');
    }
}
