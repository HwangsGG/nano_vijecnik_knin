<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Mail\SendMailNewUserAddAdmin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request['q'];
        $sortBy=$request['sort'];
        $sortDesc = $request['desc']== 'false' ? 'asc' : 'desc' ;
        $perPage = $request['perPage'];

        //ako nema broja po stranici stavi na najmanji
        if (empty($perPage)){
            $perPage=$this->pageOptions[0];
        }

        //ako nema sortiranja sortiraj po nazivu
        if(empty($sortBy)){
            $sortBy='name';
        }
        //provjeri koliko stavki ima u bazi
        $how=User::count();

        //ako je broj 500 koji oznacava prikaz sviju ili ako je broj veci od ukupnog broja prikazi samo max broj iz baze
        if($perPage==500 or $perPage>$how){
            $perPage = $how;
        }

        $all = User::where(function($query) use ($search){
            $query->where('name','LIKE',"%$search%")
                ->Orwhere('email','LIKE',"%$search%")
                ->orWhereHas('roles', function ($query) use ($search) {
                    $query->where('name','LIKE',"%$search%");
                });
        })->withCount('roles')
            ->with('roles')
            ->orderBy($sortBy,$sortDesc)
            ->paginate($perPage);

        return $all;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        global $message;

        if(!$this->user->can('user-create')){
            Log::alert('NEMATE OVLASTI ZA UNOS STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate dozvole. Obratite se Administratoru');
        }

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users|max:191',
            'roles' => 'sometimes|array|min:1',
        ],
        [
            'name.required' => 'Niste unijeli naziv!',
            'name.unique' => 'Odaberite drugi naziv. Taj naziv već postoji!',
            'name.string' => 'Naziv mora biti tekst!',
            'name.max' => 'Naziv može najviše sadržavati :max slova!',
            'email.required' => 'Niste unijeli email!',
            'email.unique' => 'Odaberite drugi email. Taj email je zauzet!',
            'email.email' => 'Email mora biti u formatu netko@domena.hr!',
            'roles.required' => 'Niste odabrali razinu prava!',
            'roles.array' => 'Niste odabrali razinu prava!',
            'roles.min' => 'Minimalno morate odabrali :min razinu prava za korisnika kojeg unosite!',

        ]);

        $random_password = Str::random(12);

        $details = [
            'password' => $random_password,
            'url' => URL::to('/settings/password'),
        ];

        Mail::to($request->email)->send(new SendMailNewUserAddAdmin($details));

        $user = new User(array(
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at'=> Carbon::now(),
            'password'=> bcrypt($random_password),
        ));

        if ($user->save()) {

            $message .= 'Uspješno unesen korisnik "' . $request['name']. '"<br>';

            if($user->syncRoles($request->roles)){
                $r=Role::select('name')->find($request->roles)->toArray();
                $roles= Arr::flatten($r);

                $message .= 'Uspješno povezan korisnik "'.$request['name'].'" s razinom/nama prava' . json_encode($roles). '"<br>';
            }

            Log::info($message.'Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

            return $this->sendResponse('USPJEŠAN UNOS', $message);
        } else {
            $message .= 'Nauspjeh - Korisnik "' . $request['name']. '" nije unesen.<br>';
            Log::error('Nauspjeh - Korisnik "' . $request['name']. '" nije unesen.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA UNOSA', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        global $message;

        if(!$this->user->can('user-edit')){
            Log::alert('NEMATE OVLASTI ZA AŽUIRANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate dozvole. Obratite se Administratoru');
        }

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'roles' => 'sometimes|array|min:1',

        ],[
            'name.required'=>'Niste unijeli ime i prezime!',
            'name.string'=>'Ime i prezime mora biti tekst!',
            'name.max'=>'Ime i prezime može sadržavati najviše :max znakova!',
            'email.required'=>'Niste unijeli email adresu!',
            'email.string'=>'Email mora biti tekst!',
            'email.max'=>'Email može sadržavati najviše :max znakova!',
            'email.email'=>'Email mora biti u formatu primjer@domena.hr!',
            'email.unique'=>'Drugi korisnik je registrian s tim mailom!',
            'roles.required' => 'Niste odabrali razinu prava!',
            'roles.array' => 'Niste odabrali razinu prava!',
            'roles.min' => 'Minimalno morate odabrali :min razinu prava za korisnika kojeg unosite!',
            ]

        );

        if ($user->update($request->all())) {
            Log::info('Uspješno ažuriran korisnik "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

            $message .= 'Uspješno ažuriran korisnik "' . $request['name']. '"<br>';

            if($user->syncRoles($request->roles)){
                $r=Role::select('name')->find($request->roles)->toArray();
                $roles= Arr::flatten($r);

                //$message .= 'Uspješno povezana razina prava s "' . $request['name']. '" ovlastima"' . json_encode($permissions). '"<br>';
                Log::info('AŽURIRANO - Uspješno povezan korisnik "' . $request['name']. '" s razinama prava"' . json_encode($roles). '');
            }

            return $this->sendResponse('AŽURIRANO', $message);

        } else {
            $message .= 'Nauspjeh - Korisnik "' . $request['name']. '" nije ažuriran.<br>';
            Log::error('Nauspjeh - Korisnik "' . $request['name']. '" nije ažuriran.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA AŽURIRANJA', $message);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {

        //napraviti funkciju kad se upisu tablice ovisnosti
        return $this->sendResponseError('GREŠKA BRISANJA', 'Korisnik "'.$user->name. '" se ne može obrisati jer funkcija nije napravljena ;)');
        /*if(!$this->user->can('user-delete')){
            Log::alert('NEMATE OVLASTI ZA BRISANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }

        $check = $user->users()->count();

        if($check != 0){
            return $this->sendResponseError('GREŠKA BRISANJA', 'Korisnik "'.$user->name. '" se ne može obrisati zbog restrikcija');

        }else {
            if ($user->delete()) {
                Log::info('Uspješno obrisan korisnik "' . $user['name'] . '".  Korisnik : ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
                return $this->sendResponse('OBRISANO','Korisnik "'.$user->name. '" je uspješno obrisan');
            } else {
                Log::error('Nauspjeh - Korisnik "' . $user['name'] . '" nije obrisan . Korisnik : '.$this->name .' '.$this->last_name.' - '.$this->email.'');
                return $this->sendResponseError('GREŠKA BRISANJA', 'Korisnik "'.$user['name']. '" nije obrisan');
            }
        }*/
    }


    // CUSTOM FUNCTIONS

    public function permissions(Request $request, User $user){

        if(!$this->user->can('user-direct-permission')){
            Log::alert('NEMATE OVLASTI ZA AŽUIRANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate dozvole. Obratite se Administratoru');
        }

        $this->validate($request,[
            'permissions' => 'required|array|min:1',

        ],[
                'permissions.required' => 'Niste odabrali dozvolu!',
                'permissions.array' => 'Niste odabrali dozvolu!',
                'permissions.min' => 'Minimalno morate odabrali :min dozvolu za korisnika kojeg unosite!',
            ]

        );

        if($user->syncPermissions($request->permissions)){
            $p=Permission::select('name')->find($request->permissions)->toArray();
            $permissions= Arr::flatten($p);

            Log::info('AŽURIRANO - Uspješno dodane dozvole korisniku "' . $user['name']. '". Broj dozvola: "' . json_encode($permissions). '');
            return $this->sendResponse('AŽURIRANO', 'Uspješno dodan broj dozvola: "'.count($permissions).'" za korisnika "'.$user->name.'"');
        }else{
            Log::info('NEUSPJEH - Dozvole nisu dodjeljene korisniku "' . $user['name']. '"');
            return $this->sendResponseError('NEUSPJEH', 'Dozvole nisu dodjeljene korisniku "' . $user['name'].'"');

        }




    }
}
