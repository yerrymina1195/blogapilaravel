<?php
namespace App\Http\Controllers;
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

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'verifyAccount']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $random = Str::random(40);
        $domain = URL::to('/');
        $url = $domain . '/verify-email/' . $random;

        $data['url'] = $url;
        $data['email'] = $request->email;
        $data['title'] = 'Email verification';
        $data['body'] = "click here to verify your email";


        Mail::send('verifyMail', ['data' => $data], function ($message) use ($data) {
            $message->to($data['email'])->subject($data['title']);
        });


        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)],
            ['tokenemail' => $random],
        ));

        return response()->json([
            'message' => 'User successfully registered and a email verification has send to ' . "{$request->email}",
            'user' => $user
        ], 201);
    }


    public function verifyAccount($token)
    {
        $verifyUser = User::where('tokenemail', $token)->first();



        $message = 'Sorry your email cannot be identified.';

        if (!is_null($verifyUser)) {
            $user = $verifyUser;

            if (!$user->isverified) {
                $dateAndTime = Carbon::now()->format('Y-m-d H:i:s');
                $verifyUser->isverified = 1;
                $verifyUser->email_verified_at = $dateAndTime;
                $verifyUser->save();
                $message = "Your e-mail is verified. You can now login.";
                return view('mailsuccess');
            } else {
                $message = "Your e-mail is already verified. You can now login.";
                return view('alreadyVerified');
            }
        }

        return view('404')->with('message', $message);
        //   return redirect()->route('welcome')->with('message', $message);
    }






    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }



    public function updateUserProfil(Request $request)
    {



        try {

            $user = User::findOrFail(auth()->user()->id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->user()->id)],
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $data = $validator->validated();

            $user->update([
                'email' => $data['email'],
                'name' => $data['name'],
            ]);

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }





    // public function logout(){
    //     if (Auth::check()) {
    //         Auth::user()->token()->revoke();
    //         return response()->json(['success' =>'Successfully logged out of application'],200);
    //     }else{
    //         return response()->json(['error' =>'api.something_went_wrong'], 500);
    //     }
    // }
}