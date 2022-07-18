<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GroupCOntroller extends BaseController
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
        $how=Group::count();

        //ako je broj 500 koji oznacava prikaz sviju ili ako je broj veci od ukupnog broja prikazi samo max broj iz baze
        if($perPage==500 or $perPage>$how){
            $perPage = $how;
        }

        if($this->user->hasRole(['SUDO','Admin','Super admin'])){
            $all = Group::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                        ->orWhere('description','LIKE',"%$search%")
                        ->orWhereHas('markers', function ($query) use ($search) {
                            $query->where('name','LIKE',"%$search%");
                        });
            })->withCount('markers')
                ->with('markers','user')
                ->orderBy($sortBy,$sortDesc)
                ->paginate($perPage);

            return $all;

        }else if($this->user->hasRole('Teacher')){
            $all = Group::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('description','LIKE',"%$search%")
                    ->orWhereHas('markers', function ($query) use ($search) {
                        $query->where('name','LIKE',"%$search%");
                    });
            })->withCount('markers','user')
                ->where('user_id',$this->id)
                ->with('markers')
                ->orderBy($sortBy,$sortDesc)
                ->paginate($perPage);
            return $all;
        }
    }

    /**
     *Display a groups all or group from user.
     *
     */
    public function index_all(){
        if ($search = \Request::get('q')) {
            if($this->user->hasRole(['SUDO','Admin','Super admin'])) {
                return response()->json(
                    Group::where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%");
                    })
                        ->get());
            }

            return response()->json(
                Group::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%");
                    })
                    ->where('user_id',$this->user->id)
                    ->get());
        }
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
       /* if(!$this->user->can('group-store')){
            Log::error('NEMATE OVLASTI ZA UNOS STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }*/

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'description' => 'required|string|max:999',
            'wordDoc' => 'required|mimes:doc,docx'
        ],
        [
            'name.required' => trans('validation.custom.name.required'),
            'name.string' => trans('validation.custom.name.string'),
            'name.max' => trans('validation.custom.name.max'),
            'description.required' => trans('validation.custom.description.required'),
            'description.string' => trans('validation.custom.description.string'),
            'description.max' => trans('validation.custom.description.max'),
            'wordDoc.string' => trans('validation.custom.wordDoc.required'),
            'wordDoc.max' => trans('validation.custom.wordDoc.mimes'),
        ]);

        $group = new Group($request->all());
        $group->setAttribute('user_id',$this->user->id);

        if ($group->save()) {
            Log::info('Uspješno unesena grupa "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

            if($request->hasFile('wordDoc')){

                $file = $request->file('wordDoc');

                $ext = $file->extension();

                if(Storage::disk('public')->putFileAs('groups/'.$this->id,$file,$group->id.'.'.$ext)){

                    $group->path_word='/storage/groups/'.$this->id.'/'.$group->id.'.'.$ext;
                    $group->update();

                }
            }
            return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.group_store',['group'=> $request['name']]));
        }

        Log::error('Nauspjeh - Grupa "' . $request['name']. '" nije unesena.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
        return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.group_store_error',['group'=> $request['name']]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Group $group)
    {
        return response()->json(
            $group->load(['markers','user'])->loadCount('markers')

        );

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Group $group)
    {
        if(!$this->user->hasAnyRole(['SUDO','Super Admin']) && $group['user_id']!=$this->user->id){
            Log::error('NEMATE OVLASTI ZA AŽURIRANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }

        $current_data=[
            'name'=> $group->name,
            'description' => $group->description
        ];

        $this->validate($request,[
            'name' => 'required|string|max:191',
            'description' => 'required|string|max:999',
        ],
        [
            'name.required' => trans('validation.custom.name.required'),
            'name.string' => trans('validation.custom.name.string'),
            'name.max' => trans('validation.custom.name.max'),
            'description.required' => trans('validation.custom.description.required'),
            'description.string' => trans('validation.custom.description.string'),
            'description.max' => trans('validation.custom.description.max'),

        ]);

        if ($group->update($request->all())) {
            Log::info('Uspješno ažurirana grupa "' . $request['name'].'" - prethodni podatci: "' . json_encode($current_data).'". Korisnik: '.$this->name .' '.$this->last_name .' - '.$this->email.'');
            return $this->sendResponse(trans('validation.custom.updated'), trans('validation.custom.group_update',['group'=> $request['name']]));
        }

        Log::error('Nauspjeh - Grupa "' . $request['name']. '" nije ažurirana.  Korisnik: '.$this->name .' '.$this->last_name.' - '.$this->email.'');
        return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.group_update_error',['group'=> $request['name']]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Group $group)
    {
        if(!$this->user->hasAnyRole(['SUDO','Super Admin']) && $group['user_id']!=$this->user->id){
            Log::error('NEMATE OVLASTI ZA BRISANJE STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.permission_error'));
        }

        $extension = ['.doc','.docx',];

        //delete group and dependices
        foreach ($extension as $ext){
            $path = public_path('storage/groups/'.$group->user_id.'/'.$group->id.''.$ext);


            if(file_exists($path)){//da li postoji taj file...
                if(unlink($path)){//obrisi file
                    Log::emergency('Uspješno obrisan dokument grupe  "' . $group['name']. '" putanja -  '.$path.'. Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
                }
                else{
                    Log::alert('NEUSPJEH - Dokument nije obrisan  "' . $group['name']. '" putanja '.$path.'. Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
                }
            }

        }


        if ($group->delete()) {
            Log::info('Uspješno obrisana grupa "' . $group['name'] . '- podatci grupe: '.$group.'".  Korisnik : ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponse(trans('validation.custom.deleted'),trans('validation.custom.group_destroy',['group'=> $group['name']]));
        }

        Log::error('Nauspjeh - Grupa "' . $group['name'] . '" nije obrisana . Korisnik : '.$this->name .' '.$this->last_name.' - '.$this->email.'');
        return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.group_destroy_error',['group'=> $group['name']]));

    }
}
