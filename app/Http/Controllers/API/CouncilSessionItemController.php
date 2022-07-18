<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CouncilSessionItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class CouncilSessionItemController extends Controller
{
    public function store(Request $request){

        global $message;

        $validator = Validator::make($request->all(), [
            'council_session_id'=> 'required|numeric|exists:council_sessions,id',
            'council_session_item_type_id'=> 'required|numeric|exists:council_session_item_types,id',
            'name'=>'required|string',
            'data'=>'required',
            'locked'=> 'required|numeric',
            'council_session_item_id_below' => 'required|numeric'
        ],[

        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $council_session_item = new CouncilSessionItem($request->all());
        $council_session_item->active=1;


        if ($request['council_session_item_id_below']>0){

            $all = CouncilSessionItem::where('council_session_id',$request['council_session_id'])->orderBy('item_number','ASC')->get();

            $number = 1;
            foreach ($all as  $item){

                if ($number == $request['council_session_item_id_below']){

                    try{
                        $council_session_item->item_number=($number);
                        $council_session_item->saveOrFail();
                        $message.='Uspješno unesena tocka dnevnog reda sa nazivom "'.$council_session_item->name.' i dodijeljenim ID-om: "'.$council_session_item->id;
                        $number++;

                    } catch(\Exception $e) {
                        return response()
                            ->json([
                                'status' =>$e->getCode(),
                                'message'=> $e->getMessage()
                            ]);
                    }

                    $new = CouncilSessionItem::find($item->id);
                    $new->item_number=($number);
                    try{
                        $new->saveOrFail();
                        $message.='Uspješno promijenjena tocka denvnog reda sa nazivom "'.$council_session_item->name.' i dodijeljenim ITEM NUMBER: "'.($number);


                    } catch(\Exception $e) {
                        return response()
                            ->json([
                                'status' =>$e->getCode(),
                                'message'=> $e->getMessage()
                            ]);
                    }

                }else if ($number > $request['council_session_item_id_below']+1){
                    $new = CouncilSessionItem::find($item->id);
                    $new->item_number=($number);
                    try{
                        $new->saveOrFail();
                        $message.='Uspješno promijenjena tocka dnevnog reda sa nazivom "'.$council_session_item->name.' i dodijeljenim ITEM NUMBER: "'.$number.'"  ';

                    } catch(\Exception $e) {
                        return response()
                            ->json([
                                'status' =>$e->getCode(),
                                'message'=> $e->getMessage()
                            ]);
                    }

                }
                $number++;


            }
        }
        else{

            $last_item = CouncilSessionItem::where('council_session_id',$request['council_session_id'])->orderBy('item_number','DESC')->first();

            try{
                $council_session_item->item_number=($last_item->item_number+1);
                $council_session_item->saveOrFail();
                $message.='Uspješno unesena tocka dnevnog reda sa nazivom "'.$council_session_item->name.' i dodijeljenim ID-om: "'.$council_session_item->id;

            } catch(\Exception $e) {
                return response()
                    ->json([
                        'status' =>$e->getCode(),
                        'message'=> $e->getMessage()
                    ]);
            }


        }

        return response()
            ->json([
                'status_code' =>200,
                'message'=> $message
            ]);
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'id'=>'required|numeric|exists:council_session_items,id',
            'data'=>'required',
            'locked'=> 'required',
            'council_session_id'=>'required|numeric|exists:council_sessions,id',
        ],[
            'id.required' => 'Niste unijeli :attribute',
            'id.numeric' => ':attribute mora biti broj',
            'id.exists' => 'ID koji ste unijeli ne postoji!',
            'data.required' => 'Niste unijeli :attribute',
            'locked.required' => 'Niste unijeli :attribute',
            'council_session_id.required' => 'Niste unijeli :attribute',
            'council_session_id.numeric' => ':attribute mora biti broj (long)',
            'council_session_id.exists' => ':attribute ne odgovara podatcima (ne postoji)',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $id = $request['id'];
        $data = $request['data'];
        $locked = $request['locked'];
        $council_session_id = $request['session_id'];

        $council_session_item = CouncilSessionItem::find($id);

        $council_session_item->data = $data;
        $council_session_item->locked = $locked;
        $council_session_item->council_session_id = $council_session_id;


        try{
            $council_session_item->saveOrFail();

            return response()
                ->json([
                    'status_code' =>Response::HTTP_OK,
                    'message'=> 'OK'
                ]);

        } catch(\Exception $e) {

            return response()
                ->json([
                    'status_code' =>$e->getCode(),
                    'message'=> $e->getMessage()
                ]);
        }

    }
}
