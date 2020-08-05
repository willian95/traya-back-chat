<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\BackpackUser as User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Socialite;
use App\Profile;

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
  protected $redirectTo = '/dashboard';

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }

  /**
  * Redirect the user to the provider authentication page.
  *
  * @return \Illuminate\Http\Response
  */
  public function redirectToProvider($driver)
  {
    return Socialite::driver($driver)->redirect();
  }

  /**
  * Obtain the user information from provider.
  *
  * @return \Illuminate\Http\Response
  */
  public function handleProviderCallback($driver)
  {
    try {
      $user = Socialite::driver($driver)->user();
      /*
      +token: "ya29.Glv5BjQFXWrHsafb6q6pwaSYmKi9GJOrRnXpY-9iF5o1Y9leLoR3DIYDH9UgfZL7yZ3zDDXR7z9-hAiWr2e-ZxGkExqqe7bkppToQvbPJI93a0rXRAh2c6GhtA2i"
      +id: "118176655939830272281"
      +nickname: null
      +name: "Sabas Vega"
      +email: "sabascarlosed+uardo@gmail.com"
      +avatar: "https://lh4.googleusercontent.com/-XVJOtEN2NGk/AAAAAAAAAAI/AAAAAAAAAAA/ACHi3re4l-cDLNBGqkVd5zHu_sgAxuIF6g/s50-mo/photo.jpg"

      */
    } catch (\Exception $e) {
      return redirect()->route('login');
    }

    $existingUser = User::where('email', $user->getEmail())->first();
    $token='test';
    if ($existingUser) {
      try {
           // verify the credentials and create a token for the user
           if (! $token = JWTAuth::fromUser($existingUser)) {
               return response()->json(['error' => 'invalid_credentials'], 401);
           }
       } catch (JWTException $e) {
           // something went wrong
           return response()->json(['error' => 'could_not_create_token'], 500);
       }
    } else {
      $newUser                    = new User;
      $newUser->provider_name     = $driver;
      $newUser->provider_id       = $user->getId();
      $newUser->name              = $user->getName();
      $newUser->email             = $user->getEmail();
      $newUser->save();
      $newUser->assignRole('Ofertante');
      $newUser->roles()->sync([$request->rol_id]);

      try {
           // verify the credentials and create a token for the user
           if (! $token = JWTAuth::fromUser($newUser)) {
               return response()->json(['error' => 'invalid_credentials'], 401);
           }
       } catch (JWTException $e) {
           // something went wrong
           return response()->json(['error' => 'could_not_create_token'], 500);
       }

       $profile=Profile::create([
         'phone'=>'00000000',
         'image'=>$user->getAvatar(),
         'positive_calification'=>0,
         'negative_calification'=>0,
         'user_id'=>$newUser->id
       ]);
    }

    // dd($user,$existingUser,$token);

    return response()->json(['token'=>$token]);


    return redirect($this->redirectPath());
  }
}
