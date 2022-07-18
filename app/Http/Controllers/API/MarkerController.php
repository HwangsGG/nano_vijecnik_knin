<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Group;
use App\Models\Marker;
use App\Models\MarkerGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \Marker as MarkerPATT;

class MarkerController extends BaseController
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
        $how=Marker::count();

        //ako je broj 500 koji oznacava prikaz sviju ili ako je broj veci od ukupnog broja prikazi samo max broj iz baze
        if($perPage==500 or $perPage>$how){
            $perPage = $how;
        }

        if($this->user->hasRole(['SUDO','Admin','Super admin'])){
            $all = Marker::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%");
                /*->orWhereHas('permissions', function ($query) use ($search) {
                    $query->where('name','LIKE',"%$search%");
                });*/
            })/*->withCount('permissions')*/
            /*->with('permissions')*/
            ->with('groups')
            ->orderBy($sortBy,$sortDesc)
                ->paginate($perPage);

            return $all;
        }else if($this->user->hasRole('Teacher')){
            $all = Marker::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('description','LIKE',"%$search%");
                /*->orWhereHas('permissions', function ($query) use ($search) {
                    $query->where('name','LIKE',"%$search%");
                });*/
            })/*->withCount('permissions')*/
            /*->with('permissions')*/
                ->where('user_id',$this->id)
                ->with('groups')
            ->orderBy($sortBy,$sortDesc)
                ->paginate($perPage);

            return $all;
        }


    }


    /**
     *
     */
    public function index_all(){
        if ($search = \Request::get('q')) {
            if($this->user->hasRole(['SUDO','Admin','Super admin'])) {
                return response()->json(
                    Marker::where(function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%$search%")
                            ->orWhere('description','LIKE',"%$search%");
                    })
                        ->get());
            }

            return response()->json(
                Marker::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('description','LIKE',"%$search%");
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

    public function checkName(Request $request){

        $current_name = $request['name'];
        $counter = 1;
        Log::info($current_name);
        while(Marker::where('name',$current_name)->count()>0){
            $current_name .=' '. $counter;
            $counter++;
        }

        return $current_name;
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        global $message;
        $current_path=getcwd();

        /*if(!$this->user->can('marker-create')){
            Log::alert('NEMATE OVLASTI ZA UNOS STAVKE.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError('GREŠKA', 'Nemate ovlasti. Obratite se Administratoru');
        }*/

        $this->validate($request,[
            'name'=>'required|string|max:100',
            'description'=>'required|string|max:999',
            'type'=> 'required|string|max:100',
            'text'=>'sometimes|required_if:type,==,text|nullable|max:999',
            //'url_video'=> 'required_if:type,==,video|url',
           // 'video' => 'required_if:type,==,video|nullable|mimes:mp4',
            //'picture' => 'required_if:type,==,picture|nullable|mimes:jpg,jpeg,png',
           // 'models' => 'required_if:type,==,models|sometimes|custom_extension:glb'

        ],
        [
            'name.required' => trans('validation.custom.name.required'),
            'name.string' => trans('validation.custom.name.string'),
            'name.max' => trans('validation.custom.name.max'),
            'description.required' => trans('validation.custom.description.required'),
            'description.string' => trans('validation.custom.description.string'),
            'description.max' => trans('validation.custom.description.max'),

            'text.required_if' => 'Niste unijeli tekst!',
            'video.required_if' => 'Niste unijeli video!',
            'video.mimes' => 'Video mora biti u formatu :values!',
            'picture.required_if' => 'Niste unijeli sliku!',
            'picture.mimes' => 'Slika mora biti u formatu :values!',
            'models.custom_extension'=> 'Model mora biti u formatu glb',

        ]);

        $marker = new Marker($request->all());
        $marker->setAttribute('user_id',$this->user->id);


        if ($request['type']==='video'){

            if ($marker->save()) {
                $message .= 'Uspješno unesen marker "' . $request['name']. '"<br>';

                if( Storage::disk('public')->put('markers/'.$this->id.'/'.$marker->id.'.png',base64_decode(\Milon\Barcode\Facades\DNS2DFacade::getBarcodePNGCustom(''.$marker->id.'', 'QRCODE',50,50,array(255, 255, 255),array(0,0,0))))){


                    $i = new MarkerPATT(public_path('/storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    if ($i->status == 'error'){
                        Log::error($i->msg);
                        /*die($i->msg);*/
                    }

                    $i->saveMarker(storage_path('dependies/nft/img/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    $i->savePatt(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.patt'));

                    if($request->hasFile('video')){

                        $file = $request->file('video');

                        $ext = $file->extension();
                        if(Storage::disk('public')->putFileAs('markers/'.$this->id,$file,$marker->id.'.'.$ext)){

                            $marker->image_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.png';
                            $marker->file_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.patt';
                            $marker->video_path='/storage/markers/'.$this->id.'/'.$marker->id.'.'.$ext;
                            $marker->iset_files='storage/markers/'.$this->id.'/'.$marker->id.'';
                            $marker->update();

                           /* chdir(storage_path('dependies/nft'));
                            Log::warning(getcwd());
                            $terminal="node app.js -i ";
                            $terminal.='img/'.$marker->id.'.jpeg -b='.$this->id;
                            Log::warning($terminal);
                            exec($terminal);
                            chdir($current_path);*/
                        }
                    }
                }

                Log::info('Uspješno unesen marker "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

                return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.marker_store',['marker'=> $request['name']]));
            }
            $message .= 'Nauspjeh - Marker "' . $request['name']. '" nije unesen.<br>';
            Log::error($message.'.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            //return $this->sendResponseError('GREŠKA UNOSA', $message);
            return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.marker_store_error',['marker'=> $request['name']]));




        }
        if($request['type']==='text'){

        if ($marker->save()) {
            $message .= 'Uspješno unesen marker "' . $request['name']. '"<br>';

            //$create_marker =  \Milon\Barcode\Facades\DNS2DFacade::getBarcodeSVG('70', 'DATAMATRIX',50,50);

            if( Storage::disk('public')->put('markers/'.$this->id.'/'.$marker->id.'.png',base64_decode(\Milon\Barcode\Facades\DNS2DFacade::getBarcodePNGCustom(''.$marker->id.'', 'QRCODE',50,50,array(255, 255, 255),array(0,0,0))))){


                $i = new MarkerPATT(public_path('/storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                if ($i->status === 'error'){
                    Log::error($i->msg);
                    /*die($i->msg);*/
                }

                $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                $i->savePatt(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.patt'));

                $marker->image_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.png';
                $marker->file_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.patt';
                $marker->update();

            }


            Log::info('Uspješno unesen marker "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
            return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.marker_store',['marker'=> $request['name']]));
           // return $this->sendResponse('USPJEŠAN UNOS', $message);
        }

            $message .= 'Nauspjeh - Marker "' . $request['name']. '" nije unesen.<br>';
            Log::error('Nauspjeh - Marker "' . $request['name']. '" nije unesen.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
           // return $this->sendResponseError('GREŠKA UNOSA', $message);
            return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.marker_store_error',['marker'=> $request['name']]));


        }
        if ($request['type']==='picture'){

            if ($marker->save()) {
               // Log::alert("Isa u IF");
                $message .= 'Uspješno unesen marker "' . $request['name']. '"<br>';

                if( Storage::disk('public')->put('markers/'.$this->id.'/'.$marker->id.'.png',base64_decode(\Milon\Barcode\Facades\DNS2DFacade::getBarcodePNGCustom(''.$marker->id.'', 'QRCODE',50,50,array(255, 255, 255),array(0,0,0))))){


                    $i = new MarkerPATT(public_path('/storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    if ($i->status === 'error'){
                        Log::error($i->msg);
                        /*die($i->msg);*/
                    }

                    //$i->saveMarker(storage_path('dependies/nft/img/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    $i->savePatt(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.patt'));

                    if($request->hasFile('picture')){

                        $file = $request->file('picture');

                        $ext = $file->extension();
                        if(Storage::disk('public')->putFileAs('markers/'.$this->id,$file,$marker->id.'_orginal.'.$ext)){

                            $marker->image_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.png';
                            $marker->file_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.patt';
                            $marker->video_path='/storage/markers/'.$this->id.'/'.$marker->id.'_orginal.'.$ext;
                            $marker->iset_files='storage/markers/'.$this->id.'/'.$marker->id.'';
                            $marker->update();

                            /*chdir(storage_path('dependies/nft'));
                            Log::warning(getcwd());
                            $terminal="node app.js -i ";
                            $terminal.='img/'.$marker->id.'.jpeg -b='.$this->id;
                            Log::warning($terminal);
                            exec($terminal);
                            chdir($current_path);*/
                        }
                    }
                }

                Log::info('Uspješno unesen marker "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
                return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.marker_store',['marker'=> $request['name']]));
                //return $this->sendResponse('USPJEŠAN UNOS', $message);
            }
            $message .= 'Nauspjeh - Marker "' . $request['name']. '" nije unesen.<br>';
            Log::error('Nauspjeh - Marker "' . $request['name']. '" nije unesen.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            //return $this->sendResponseError('GREŠKA UNOSA', $message);
            return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.marker_store_error',['marker'=> $request['name']]));




        }
        if ($request['type']==='models'){
            if ($marker->save()) {
                $message .= 'Uspješno unesen marker "' . $request['name']. '"<br>';

                if( Storage::disk('public')->put('markers/'.$this->id.'/'.$marker->id.'.png',base64_decode(\Milon\Barcode\Facades\DNS2DFacade::getBarcodePNGCustom(''.$marker->id.'', 'QRCODE',50,50,array(255, 255, 255),array(0,0,0))))){


                    $i = new MarkerPATT(public_path('/storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    if ($i->status === 'error'){
                        Log::error($i->msg);
                        /*die($i->msg);*/
                    }

                    $i->saveMarker(storage_path('dependies/nft/img/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.jpeg'));
                    $i->saveMarker(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.png'));
                    $i->savePatt(public_path('storage/markers/'.$this->id.'/'.$marker->id.'.patt'));

                    if($request->hasFile('models')){

                        $file = $request->file('models');

                        $ext = $file->extension();
                        if(Storage::disk('public')->putFileAs('markers/'.$this->id,$file,$marker->id.'.glb')){

                            $marker->image_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.png';
                            $marker->file_marker='/storage/markers/'.$this->id.'/'.$marker->id.'.patt';
                            $marker->video_path='/storage/markers/'.$this->id.'/'.$marker->id.'.glb';
                            $marker->iset_files='storage/markers/'.$this->id.'/'.$marker->id.'';
                            $marker->update();

                            chdir(storage_path('dependies/nft'));
                            Log::warning(getcwd());
                            $terminal="node app.js -i ";
                            $terminal.='img/'.$marker->id.'.jpeg -b='.$this->id;
                            Log::warning($terminal);
                            exec($terminal);
                            chdir($current_path);
                        }
                    }
                }

                Log::info('Uspješno unesen marker "' . $request['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');
                return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.marker_store',['marker'=> $request['name']]));
            }

            $message .= 'Nauspjeh - Marker "' . $request['name']. '" nije unesen.<br>';
            Log::error('Nauspjeh - Marker "' . $request['name']. '" nije unesen.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
            return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.marker_store_error',['marker'=> $request['name']]));
        }

        return $this->sendResponseError('ERROR', 'NOT VALID FUNCTION');

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Marker $marker)
    {
        $this->validate($request,[
            'name'=>'required|string|max:100',
            'description'=>'required|string|max:999',

        ],
        [
            'name.required' => trans('validation.custom.name.required'),
            'name.string' => trans('validation.custom.name.string'),
            'name.max' => trans('validation.custom.name.max'),
            'description.required' => trans('validation.custom.description.required'),
            'description.string' => trans('validation.custom.description.string'),
            'description.max' => trans('validation.custom.description.max'),
        ]);



        if ($marker->update(['name'=>$request['name'],'description'=>$request['description']])) {
            Log::info('Uspješno ažuriran marker "' . $request['name'].'".  Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
            return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.marker_update',['marker'=> $request['name']]));

        }

        Log::error('Nauspjeh - Marker "' . $request['name']. '" nije ažuriran . Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
        return $this->sendResponseError(trans('validation.custom.error'), trans('validation.custom.marker_update_error',['marker'=> $request['name']]));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Marker $marker)
    {
        $extension = ['.png','.jpeg','.jpg','_orginal.png','_orginal.jpg','_orginal.jpeg','.patt','.iset','.fset','.fset3','.mp4','.mp3'];

        //delete markers and dependices
        foreach ($extension as $ext){
            $path = public_path('storage/markers/'.$marker->user_id.'/'.$marker->id.''.$ext);

            Log::emergency($path);

            if(file_exists($path)){//da li postoji taj file...neki markeri imaju samo neke filove ovisno o vrsti pa se mora provjeriti da li postoji inace ga nece pronaci i javiti ce error
                if(unlink($path)){//obrisi file
                    Log::emergency('Uspješno obrisan marker  "' . $marker['name']. '" i njegova datoteka '.$path.'. Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
                }
                else{
                    Log::alert('NEUSPJEH - Marker nije obrisan  "' . $marker['name']. '" i njegova datoteka '.$path.'. Korisnik : ' . $this->user->name .' - ' . $this->user->email.'');
                }
            }

        }

        if ($marker->delete()) {
            Log::info('Uspješno obrisan marker "' . $marker['name']. '". Korisnik: ' .$this->name .' '.$this->last_name .' - ' .$this->email.'');

            return $this->sendResponse('USPJEŠNO BRISANJE', 'Marker uspjesno obrisan');
        }
        Log::error('Nauspjeh - Marker "' . $marker['name']. '" nije obrisan.  Korisnik: ' .$this->name .' '.$this->last_name.' - ' .$this->email.'');
        return $this->sendResponseError('ERROR', 'Marker nije obrisan');

    }

    public function mergeMarkersWithGroup(Request $request){
         $message='';

        $this->validate($request,[
            'markers_id' => 'required|array',
            'newGroup'=>'nullable|sometimes|string'
        ],[
            'markers_id.required' => trans('validation.custom.marker_id.required'),
        ]);

        //ako grupa ne postoji tj. ako se zeli unijeti nova grupa
        if($request->group_id===0){

            $group_id= Group::insertGetId(array(
                'name'=>$request['newGroup'],
                'user_id'=>$this->user->id,
                'created_at'=> now(),
                'updated_at'=> now(),
            ));

            foreach ($request->markers_id as $m){
                $marker = Marker::find($m);
                $marker_group = new MarkerGroup(array(
                    'marker_id' => $marker->id,
                    'group_id' => $group_id
                ));
                $message = $this->getMessage($marker_group, $request['newGroup'], $marker, $message);
            }
            Log::info($message);
            return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.merge_marker_store'));
           // return $this->sendResponse('OBAVIJEST',nl2br($message) );

        }

        //foreach ($request->markers_id as $marker){

            //provjeri da li je marker povezan s grupom
            $check= MarkerGroup::where('group_id',$request['group_id'])->select('marker_id')->get()->toArray();  //dohvati trenutne markere od grupe
            $check=collect($check);//pretvori u collection kako bi se moglo manipulirati podatcima
            $flattened = $check->flatten(); //metoda koja vraća jednodimenzionalno polje
            $markerIdCurrent= $flattened->all();//array u kojem se nalazi samo id markera trenutne grupe

            /*PROVJERA MARKERA KOJE NISU POVEZANE S GRUPOM*/
            $diff_select_current=array_diff($request->markers_id,$markerIdCurrent);

            if(!is_null($diff_select_current)) {
                foreach ($diff_select_current as $m) {//spoji marker i grupu
                    $marker = Marker::find($m);
                    $marker_group = new MarkerGroup(array(
                        'marker_id' => $marker->id,
                        'group_id' => $request['group_id'],
                    ));

                    $message = $this->getMessage($marker_group, $request['newGroup'], $marker, $message);
                }
            }

            /*$marker_group = new MarkerGroup(array(
                'marker_id' => $marker,
                'group_id' => $request['group_id'],
            ));

            $message = $this->getMessage($marker_group, $request['newGroup'], $marker, $message);*/
       // }
        return $this->sendResponse(trans('validation.custom.success'), trans('validation.custom.merge_marker_store'));
        //return $this->sendResponse('OBAVIJEST',nl2br($message) );


    }

    /**
     * @param MarkerGroup $marker_group
     * @param $newGroup
     * @param $marker
     * @param string $message
     * @return string
     */
    public function getMessage(MarkerGroup $marker_group, $newGroup, $marker, string $message): string
    {
        if ($marker_group->save()) {
            Log::info('Uspješno unesena grupa "' . $newGroup . ' i povezana s markerom : "' . $marker->name . '"  Korisnik : ' . $this->user->name . ' - ' . $this->user->email . '');
            $message .= 'Uspješno unesena grupa "' . $newGroup . ' i povezana s markerom: "' . $marker->name .'<br>';
            //return $this->sendResponse('OBAVIJEST',nl2br($message) );

        } else {
            Log::error('Nauspjeh - Grupa "' . $newGroup . '" nije povezana s markerom s id-om: "' . $marker->name . '" . Korisnik : ' . $this->user->name . ' - ' . $this->user->email . '');
            $message .= 'Nauspjeh - Grupa "' . $newGroup . '" nije povezana s markerom: "' . $marker->name .'<br>';
            //return $this->sendResponseError('GREŠKA',nl2br($message) );
        }
        return $message;
    }
}
