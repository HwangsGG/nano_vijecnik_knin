<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ProfileController extends BaseController
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'photo' => 'sometimes|image|mimes:jpeg,jpg,png,gif|max:10000',
        ]);

        /*provjeri da li slika postoji i ako postoji dohvati ime i dekodiraj je*/
        if($request->file()) {
            $currentPhoto = $user->photo_url;
            $image = $request->file('photo');
            $destinationPath = public_path('img/profile');
            $img = Image::make($image->path());

            $name = time().'_'.$request->photo->getClientOriginalName();
            $request->merge(['photo_url' => $name]);

            if($img->resize(468, 249, function ($constraint) {$constraint->aspectRatio();})->save($destinationPath.'/'.$name)){
                Log::info('Uspješno ažurirana slika profila za korisnika "' . $user['name']. '" Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
            }

            $userPhoto = 'img/profile/'.$currentPhoto;

            if(file_exists($userPhoto) and $userPhoto!='public/img/profile/default.png'){
                @unlink($userPhoto);
                Log::info('Treunutna slika profila obrisana i dodana nova');
            }

        }

        return tap($user)->update($request->only('name', 'email','photo_url'));
        /*        if ($user->update($request->only('name', 'email','photo_url'))) {
            Log::info('Uspješno ažurirane informacije o korisniku "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
            return $this->sendResponse($user, 'Uspješno ažurirane informacije o korisniku "' . $request['name']. '"');
        }else{
            return $this->sendResponseError('GREŠKA AŽURIRANJA', 'Informacije o korisniku nisu ažurirane. Obratite se administratoru');
        }*/

    }
}
