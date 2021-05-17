<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = 'shopee-analysis/cate';

    public function showLoginForm()
    {
        if (Auth::check()) {
            return view('shopee_cate');
        } else {
            return view('login');
        }
    }

    public function login(Request $request)
    {
        $loginInfo = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $remember = $request->has('remember_me');

        if (Auth::attempt($loginInfo, $remember)) {
            return redirect()->route('shopee.cate')->with('result', 'Login Success!');
        } else {
            return redirect()->back()->with('error', 'Login Failed, Try Again!');
        }
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

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
