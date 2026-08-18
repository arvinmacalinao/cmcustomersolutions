<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectPath = '/auth/register';
    protected $redirectAfterLogout = '/auth/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest', ['except' => 'getLogout']);
        $this->middleware('guest', ['except' => ['getLogout', 'getRegister', 'postRegister']]);
        $this->middleware('auth', ['only' => ['getRegister', 'postRegister']]);
    }

    /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password, 'flag' => true])) {
            // Authentication passed...
            return redirect()->intended('/');
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:45',
            'email' => 'required|email|max:45|unique:users',
            'password' => 'required|confirmed|min:6',
            'role_id' => 'required|integer|exists:roles,id',
            'company_id' => 'required|integer|exists:companies,id',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $this->authorize('user_mgmt');

        $hq_role = [3, 5, 10, 16, 11, 7, 14];
        $branch_role = [4, 8, 13];

        // Branch Admin, Technician (Branch), Warehouse Personnel (Branch) must not be assign to HQ; role = 4, 8, 13
        // HQ Admin, Officer-In-Charge, Physical Encoder, Receiving & Dispatching Unit, Special Case, Technician (HQ), Warehouse Personnel (HQ) must not be assign to Branch; role = 3, 5, 10, 16, 11, 7, 14
        if ( (in_array($data['role_id'], $hq_role) && $data['company_id'] != 1) ) {
            flash(trans('cdu.err_user_reg', ['reason' => 'the role selected can only be assign to HQ']), 'danger');
        } elseif ( (in_array($data['role_id'], $branch_role) && $data['company_id'] == 1) ) {
            flash(trans('cdu.err_user_reg', ['reason' => 'the role selected cannot be assign to HQ']), 'danger');
        } else {
            // pass
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'role_id' => $data['role_id'],
                'company_id' => $data['company_id'],
                'created_by' => Auth::id(),
            ]);
        }
        
        
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        // Commenting this line should help.
        //Auth::login($this->create($request->all())); 
        $this->create($request->all());

        return redirect($this->redirectPath());
    }
}
