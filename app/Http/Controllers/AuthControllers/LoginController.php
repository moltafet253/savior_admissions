<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Mews\Captcha\Captcha;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';

    public function getCaptcha(Captcha $captcha)
    {
        $captcha->builder()->build();
        session(['captcha' => $captcha->builder()->getPhrase()]);

        return $captcha->response();
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('web')->only('logout');
    }

    public function showLoginForm()
    {
        if (! Auth::check()) {
            return view('Auth.login');
        }
        //        $nationalities=Country::select('id','nationality')->get();
        //        if (!Auth::check()) {
        //            return view('Auth.fake_signup',compact('nationalities'));
        //        }
        Auth::logout();

        return redirect()->route('dashboard');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required', // Uncomment if you want to include captcha validation
        ]);

        if ($validator->fails()) {
            $this->logActivity(json_encode(['activity' => 'Login Failed', 'errors' => $validator->errors()]), request()->ip(), request()->userAgent());

            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Uncomment if you want to include captcha validation
        $captcha = $request->input('captcha');
        $sessionCaptcha = session('captcha')['key'];
        if (! password_verify($captcha, $sessionCaptcha)) {
            $this->logActivity(json_encode(['activity' => 'Login Failed (Wrong Captcha)', 'email' => $request->input('email')]), request()->ip(), request()->userAgent());

            return response()->json([
                'success' => false,
                'errors' => [
                    'captcha' => ['Captcha code is wrong or null!'],
                ],
            ]);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = User::where('email', $request->input('email'))->first();
            $userID = $user['id'];
            Session::put('id', $userID);
            Session::put('type', $user['type']);
            $this->logActivity(json_encode(['activity' => 'Login Succeeded', 'email' => $request->input('email')]), request()->ip(), request()->userAgent());

            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
                'message' => 'Login successful',
            ]);
        }

        $this->logActivity(json_encode(['activity' => 'Login Failed (Wrong Email Or Password)', 'email' => $request->input('email')]), request()->ip(), request()->userAgent());

        return response()->json([
            'success' => false,
            'errors' => [
                'loginError' => ['Invalid email or password'],
            ],
        ]);
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectPath());
    }
}
