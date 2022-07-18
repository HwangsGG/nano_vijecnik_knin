<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends BaseController
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
        $how=City::count();

        //ako je broj 500 koji oznacava prikaz sviju ili ako je broj veci od ukupnog broja prikazi samo max broj iz baze
        if($perPage==500 or $perPage>$how){
            $perPage = $how;
        }

        $all = City::where(function($query) use ($search){
                            $query->where('name','LIKE',"%$search%")
                                ->orWhere('post_code','LIKE',"%$search%");
                        })->withCount('users')
                            ->orderBy($sortBy,$sortDesc)
                            ->paginate($perPage);

        return $all;


      /*  return City::select('cities.id', 'cities.name','cities.post_code','cities.updated_at')
            ->withCount('users')
            ->orderBy('cities.name')
            ->paginate();*/
    }

    //get all cities
    public function index_all()
    {
        return City::select('id','name','post_code')->orderBy('name')->get();
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
        if(!$this->user->can('city-store')){
            Log::error('NEMATE OVLASTI ZA UNOS STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'post_code' => 'required|numeric|unique:cities|digits:5',
        ],
            [
                'name.required' => 'Niste unijeli naziv!',
                'name.string' => 'Naziv mora biti tekst!',
                'name.max' => 'Naziv može najviše sadržavati :max slova!',
                'post_code.required' => 'Niste unijeli poštanski broj!',
                'post_code.numeric' => 'Poštanski broj mora biti u brojčanom formatu!',
                'post_code.unique' => 'Poštanski broj već postoji u sustavu!',
                'post_code.digits' => 'Poštanski broj mora imati 5 znamenki!',
            ]);

        $city = new City($request->all());

        if ($city->save()) {
            Log::info('Uspješno unesen grad "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
            return $this->sendResponse('AŽURIRANO', 'Grad '.$city->name. ' je uspješno unesen');
        } else {
            Log::error('Nauspjeh - Grad "' . $request['name']. '" nije unesen.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA UNOSA', 'Grad '.$request['name']. ' nije unesen');
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
        $city=City::findOrFail($id)->with('users')->first();
        Log::info('Prikaz grada "'.$city->name.'". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
        return $this->sendResponse($city, 'Grad '.$city->name. ' je uspješno dohvaćen');
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
    public function update(Request $request, City $city)
    {
        if(!$this->user->can('city-update')){
            Log::error('NEMATE OVLASTI ZA AŽURIRANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }

        $current_name=$city->name;
        $this->validate($request,[
            'name' => 'required|string|max:191',
            'post_code' => 'required|digits:5|numeric|unique:cities,post_code,'.$city->id,
        ],
            [
                'name.required' => 'Niste unijeli naziv!',
                'name.string' => 'Naziv mora biti tekst!',
                'name.max' => 'Naziv može najviše sadržavati :max slova!',
                'post_code.required' => 'Niste unijeli poštanski broj!',
                'post_code.numeric' => 'Poštanski broj mora biti u brojčanom formatu!',
                'post_code.unique' => 'Poštanski broj već postoji u sustavu!',
                'post_code.digits' => 'Poštanski broj mora imati 5 znamenki!',
            ]);

        if ($city->update($request->all())) {
            Log::info('Uspješno ažuriran grad "' . $request['name'].'" - prethodni naziv: "' . $current_name.'". Korisnik: '.$this->name .' '.$this->last_name .' - '.$this->email.'');
            return $this->sendResponse('AŽURIRANO', 'Grad '.$city->name. ' je uspješno ažuriran');
        } else {
            Log::error('Nauspjeh - Grad "' . $request['name']. '" nije ažuriran.  Korisnik: '.$this->name .' '.$this->last_name.' - '.$this->email.'');
            return $this->sendResponseError('GREŠKA UNOSA', 'Grad '.$request['name']. ' nije ažuriran');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(City $city)
    {
        if(!$this->user->can('city-delete')){
            Log::error('NEMATE OVLASTI ZA BRISANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }

        $check = $city->users()->count();

        if($check != 0){
            return $this->sendResponseError('GREŠKA BRISANJA', 'Grad '.$city->name. ' se ne može obrisati. Korisnici su upisani pod tim gradom');

        }else {
            if ($city->delete()) {
                Log::info('Uspješno obrisan grad "' . $city['name'] . '".  Korisnik : ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
                return $this->sendResponse('OBRISANO','Grad '.$city->name. ' je uspješno obrisan');
            } else {
                Log::error('Nauspjeh - Grad "' . $city['name'] . '" nije obrisan . Korisnik : '.$this->name .' '.$this->last_name.' - '.$this->email.'');
                return $this->sendResponseError('GREŠKA BRISANJA', 'Grad '.$city['name']. ' nije obrisan');
            }
        }
    }
}
