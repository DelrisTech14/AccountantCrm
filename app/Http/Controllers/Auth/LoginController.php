<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Http\Request;
use DB;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    
    protected function authenticated(Request $request, $user)
    {
        $results = DB::select("SELECT ur.user_id,ur.website_module_id,ur.role_type_id, LOWER(r.title) as role, LOWER(m.title) as module
                FROM user_roles ur
                INNER JOIN website_modules m ON ur.website_module_id = m.id
                INNER JOIN module_roles r ON ur.role_type_id = r.id WHERE ur.user_id = ".$user->id."");
        $roles = array();
        foreach ($results as $row) {
            $roles[$row->website_module_id][] = $row->role;
        }
        Session::put('user_roles', $roles);
    }
    
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
