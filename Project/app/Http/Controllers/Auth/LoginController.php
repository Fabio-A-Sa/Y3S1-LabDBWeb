<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Mail;
use App\Models\Post;
use App\Models\User;

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
    protected $redirectTo = '/home';

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    public function getUser() {
        return $request->user();
    }

    public function home() {
        return redirect('login');
    }

    public function show() {
        $posts = Post::publicPosts()->get();
        return view('auth.login', ['posts' => $posts]);
    }

    public function recoverPassword(Request $request) {

        $user = User::where('email', '=', $request->recoverAttemp)->first();
        if (!$user) return redirect()->route('login')->with('error', "Invalid email");

        if (Hash::check($request->recoverToken, $user->password)) {

            if($request->recoverPassword1 != $request->recoverPassword2) return redirect()->route('login')->with('match_error', "Passwords don't match")
                                                                                          ->with('email_attemp', $request->recoverAttemp);
            if(strlen($request->recoverPassword1) < 6) return redirect()->route('login')->with('size_error', "Password must be at least 6 characters")
                                                                                        ->with('email_attemp', $request->recoverAttemp);
            $user->password = bcrypt($request->recoverPassword1);
            $user->save();
            return redirect()->route('login')->with('success', "Your password has been changed successfully");
        }
        return redirect()->route('login')->with('invalid_token', "Invalid token. Please try again.")
                                         ->with('email_attemp', $request->recoverAttemp);
    }

    protected function generateRandomToken(int $length) {
        
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function sendEmail(Request $request) {
        
        $user = User::where('email', '=', $request->email)->first();
        if ($user) {

            $token = $this->generateRandomToken(8);

            $data = array(  'name' => $user->name,
                            'username' => $user->username, 
                            'token' => $token               );

            Mail::send('partials.mail', $data, function($message) {
                $message->subject('Recover your password!');
                $message->from('OnlyFEUP@gmail.com','OnlyFEUP');
                $message->to('user@gmail.com', 'OnlyFEUP User');
            });
            
            $user->password = bcrypt($token);
            $user->save();
        }
    }
}