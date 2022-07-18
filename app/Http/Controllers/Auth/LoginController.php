<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\VerifyEmailException;
use App\Http\Controllers\Controller;
use App\Models\Specification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

   // protected $maxAttempts = 10; // Default is 5
  //  protected $decayMinutes = 1; // Default is 1


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function getSpecification(){

        return Specification::first();

    }

    protected function validateLogin(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'email'=>'required|string|email',
            'password'=>'required|string',
        ],[
            'email.required' => 'Niste unijeli :attribute',
            'email.string' => ':attribute mora biti tekst',
            'email.email' => 'Niste isravno unijeli :attribute',
            'password.required' => 'Niste unijeli :attribute',
            'password.string' => ':attribute mora biti tekst',
        ]);

        return $validator;

    }

    public function login(Request $request)
    {
        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status'=>Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );
            //rucno napravljena funkcija
            return response()
                ->json([
                    'errors' =>'Previše ste se puta pokušali prijaviti u kratkom vremenu. Pokušajte ponovno za: '.$seconds.' sekundi/a',
                    'status' =>Response::HTTP_TOO_MANY_REQUESTS
                ]);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return response()
            ->json([
                'errors' =>'Niste unijeli točne podatke',
                'status' =>Response::HTTP_UNPROCESSABLE_ENTITY
            ]);

    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $token = $this->guard()->attempt($this->credentials($request));

        if (! $token) {
            return false;
        }

        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $this->guard()->setToken($token);

        return true;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        $token = (string) $this->guard()->getToken();
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration - time(),
            'user'=>auth()->user(),
            'status' => Response::HTTP_OK,
            'version'=>$this->getSpecification()->version,
            'url' =>$this->getSpecification()->url
        ]);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
    }
}
