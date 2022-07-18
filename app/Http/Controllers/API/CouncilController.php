<?php

namespace App\Http\Controllers\API;

use App\Models\CouncilSessionItem;
use Illuminate\Http\Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\CouncilSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CouncilController extends Controller
{
    public function index(Request $request){

        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'count'=>'required|numeric',
            'offset'=>'required|numeric',
            'ids'=> 'sometimes|array',
            'start_time'=>'sometimes|date_format:Y-m-d',
            'end_time'=>'sometimes|date_format:Y-m-d|after:start_date'

        ],[
            'count.required' => 'Niste unijeli :attribute',
            'count.numeric' => ':attribute mora biti broj',
            'offset.required' => 'Niste unijeli :attribute',
            'offset.numeric' => ':attribute mora biti broj',
            'offset.string' => ':attribute mora biti u formatu npr. 10 ili 15',
            'ids.array' => ':attribute mora biti array npr. [1,2,3]',
            'start_time.date_format' => ':attribute mora biti u formatu Y-m-d npr. 2023-05-18',
            'end_time.date_format' => ':attribute mora biti u formatu Y-m-d npr. 2023-05-19',
            'end_time.after' => ':attribute mora biti nakon datuma :date',


        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }
        /*$this->validate($request,[
            'count'=>'required|numeric',
            'offset'=>'required|numeric',
            'ids'=> 'sometimes|string',
            'start_date'=>'sometimes|date_format:Y-m-d',
            'end_date'=>'sometimes|date_format:Y-m-d|after:start_date'
        ]);*/

        $count = $request['count'];
        $offset = $request['offset'];
        $ids = empty($request['ids']) ? '' : $request['ids'] ;
        $start_date = empty($request['start_time']) ? '' : date($request['start_time']) ;
        $end_date = empty($request['end_time']) ? '' : date( $request['end_time']) ;

        $sessions = CouncilSession::where(function( $query) use ($count,$offset,$ids,$start_date,$end_date) {

           if(!empty($ids)){
               $query->whereIn('id', $ids);
           }

           if(!empty($start_date) && !empty($end_date)){

               $query->whereBetween('date', [$start_date, $end_date]);
           }

        })->with(['council_session_item','council_session_item.council_session_item_type'])
            ->skip($offset)
            ->take($count)
            ->get();

        return response()
            ->json([
                'council_sessions' =>$sessions,
                'status' => Response::HTTP_OK
            ]);
    }


    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'id'=>'required|numeric',
            'data'=>'required',
            'locked'=> 'required',
            'start_time'=>'required|numeric',
            'end_time'=>'required|numeric|gte:start_time'

        ],[
            'id.required' => 'Niste unijeli :attribute',
            'id.numeric' => ':attribute mora biti broj',
            'data.required' => 'Niste unijeli :attribute',
            'locked.required' => 'Niste unijeli :attribute',
            'start_time.required' => 'Niste unijeli :attribute',
            'start_time.numeric' => ':attribute mora biti broj (long)',
            'end_time.required' => 'Niste unijeli :attribute',
            'end_time.numeric' => ':attribute mora biti broj (long)',
            'end_time.gte' => ':attribute vrijednost mora veći od vrijednosti :value',


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
        $start_time=gmdate("Y-m-d H:i:s",intval($request['start_time']));
        $end_time=gmdate("Y-m-d H:i:s",intval($request['end_time']));

        $council_session = CouncilSession::find($id);

        $council_session->data = $data;
        $council_session->locked = $locked;
        $council_session->start_time = $start_time;
        $council_session->end_time = $end_time;

        try{
            $council_session->saveOrFail();

            return response()
                ->json([
                    'status' =>Response::HTTP_OK,
                    'message'=> 'OK'
                ]);

        } catch(\Exception $e) {

            return response()
                ->json([
                    'status' =>$e->getCode(),
                    'message'=> $e->getMessage()
                ]);
        }

    }


    public function store(Request $request){
        //return $request->all();
        global $message;

        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'data'=>'required',
            'date'=>'required|numeric',
            'start_time'=>'required|numeric',
            'end_time'=>'required|numeric|gte:start_time',
            'locked'=> 'required',
            'active'=>'required|numeric',

            'items.*.council_session_item_type_id' => 'required|numeric|exists:council_session_item_types,id',
            'items.*.name'=>'required',
            'items.*.data'=>'required',
            'items.*.locked'=> 'required',
            'items.*.item_number'=> 'required',
        ],[
            'id.required' => 'Niste unijeli :attribute',
            'id.numeric' => ':attribute mora biti broj',
            'data.required' => 'Niste unijeli :attribute',
            'locked.required' => 'Niste unijeli :attribute',
            'start_time.required' => 'Niste unijeli :attribute',
            'start_time.numeric' => ':attribute mora biti broj (long)',
            'end_time.required' => 'Niste unijeli :attribute',
            'end_time.numeric' => ':attribute mora biti broj (long)',
            'end_time.gte' => ':attribute vrijednost mora veći od vrijednosti :value',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $council_session = new CouncilSession($request->all());
        $council_session->date =gmdate("Y-m-d H:i:s",intval($request['date']));
        $council_session->start_time =gmdate("Y-m-d H:i:s",intval($request['start_time']));
        $council_session->end_time =gmdate("Y-m-d H:i:s",intval($request['end_time']));


        try{
            $council_session->saveOrFail();
            $message.='Uspješno unesena sjednica sa nazivom "'.$council_session->name.' i dodijeljenim ID-om: "'.$council_session->id;

        } catch(\Exception $e) {
            return response()
                ->json([
                    'status' =>$e->getCode(),
                    'message'=> $e->getMessage()
                ]);
        }

        foreach ($request->items as $item){

            $council_session_item = new CouncilSessionItem($item);
            $council_session_item->council_session_id= $council_session->id;
            try{
                $council_session_item->saveOrFail();
                $message.='\nUspješno povezana sjednica s tockom dnevnog reda pod nazivom: "'.$council_session_item->name;

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
                'status' => Response::HTTP_OK,
                'message'=> $message
            ]);





    }


}
