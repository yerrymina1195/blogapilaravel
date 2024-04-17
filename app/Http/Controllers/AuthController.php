<?php

namespace App\Http\Controllers;

use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyAccount']]);
    }

    public function login(Request $request)
    {
        $validator = $this->validateLogin($request->all());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = $this->validateRegistration($request->all());

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $random = Str::random(40);
        $url = URL::to('/verify-email/').'/' . $random;

        
        try {
          $user=  User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)],
                ['tokenemail' => $random]
            ));
            $user->notify(new UserNotification($url));
            // $this->sendVerificationEmail($request->email, $url);
        } catch (Exception $e) {
            return response()->json(['error' => 'User registration failed'], 500);
        }

        return response()->json([
            'message' => 'User successfully registered. Email verification sent.',
        ], 201);
    }

    public function verifyAccount($token)
    {
        try {
            $user = User::where('tokenemail', $token)->firstOrFail();
            
            if ($user->isverified) {
                return view('alreadyVerified');
            }

            $user->isverified = true;
            $user->email_verified_at = Carbon::now();
            $user->save();

            return view('mailsuccess');
        } catch (ModelNotFoundException $e) {
            return view('404')->with('message', 'Sorry, your email cannot be identified.');
        }
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    public function updateUserProfile(Request $request)
    {
        try {
            $user = auth()->user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user->update($validator->validated());

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update user profile'], 500);
        }
    }

    protected function validateLogin($data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    }

    protected function validateRegistration($data)
    {
        return Validator::make($data, [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    protected function sendVerificationEmail($email, $url)
    {
        $data = [
            'url' => $url,
            'email' => $email,
            'title' => 'Email verification',
            'body' => 'Click here to verify your email',
        ];

        Mail::send('verifyMail', ['data' => $data], function ($message) use ($data) {
            $message->to($data['email'])->subject($data['title']);
        });
    }
}
