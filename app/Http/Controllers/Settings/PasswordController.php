<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\SendMailAfterChangePasswordAdmin;
use App\Mail\SendMailNewUserAddAdmin;
use App\Models\User;
use App\Rules\MatchOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class PasswordController extends BaseController
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'current_password' => ['required', new MatchOldPassword],
            'password' => 'required|confirmed|min:6',

        ], [
            //Custom error poruke
            'current_password.required' => 'Niste unijeli lozinku!',
            'password.min' => 'Lozinka mora imati minimalno 6 znakova!',
            'password.required' => 'Niste unijeli lozinku!',
            'password_confirmation.required' => 'Niste unijeli lozinku!',
            'password_confirmation.same' => 'Lozinke se ne podudaraju!',

        ]);

        $request->user()->update([
            'password' => bcrypt($request->password),
        ]);
    }


    public function reset_password(Request $request, User $user){

        global $message;

        if(!$this->user->can('user-reset-password')){
            Log::alert('NEMATE OVLASTI ZA AŽUIRANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate dozvole. Obratite se Administratoru');
        }

        $this->validate($request, [
            'password' => 'required|confirmed|min:6',

        ], [
            //Custom error poruke
            'password.min' => 'Lozinka mora imati minimalno 6 znakova!',
            'password.required' => 'Niste unijeli lozinku!',
            'password_confirmation.required' => 'Niste unijeli lozinku!',
            'password_confirmation.same' => 'Lozinke se ne podudaraju!',
        ]);


        if($user->update(['password' => bcrypt($request->password),])){
            $message .= 'Uspješno ažurirana lozinka za korisnika "'.$user->name.'"<br>';
            Log::info($message.' Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

            //da li se nova lozinka salje korisniku na mail ili ne
            if ($request->send_mail){
                $details = [
                    'password' => $request->password,
                    'url' => URL::to('/settings/password'),
                ];

                Mail::to($user->email)->send(new SendMailAfterChangePasswordAdmin($details));

                if (Mail::failures()) {
                    $message .= 'Mail za promijenjenu lozinku nije poslan korisniku "'.$user->name.'"<br>';
                    Log::alert('NAUSPJEH - Mail za promijenjenu lozinku nije poslan korisniku "'.$user->name.'". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
                }else{
                    $message .= 'Mail je poslan korisniku na adresu "'.$user->email.'"<br>';
                    Log::info('Mail je poslan korisniku na adresu "'.$user->email.'"');
                }
            }

            return $this->sendResponse('LOZINKA PROMIJENJENA', $message);

        }else{
            $message .= 'NAUSPJEH - lozinka za korisnika "'.$user->name.'" nije promijenjena<br>';
            Log::info('NAUSPJEH - lozinka za korisnika "'.$user->name.'" nije promijenjena. Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA AŽURIRANJA LOZINKE', $message);
        }
    }
}
