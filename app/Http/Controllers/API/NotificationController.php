<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class NotificationController extends Controller
{
    public function store(Request $request){
        //return $request->all();

        global $message;

        $validator = Validator::make($request->all(), [
            'type_id'=>'required|numeric',
            'council_session_item_id'=>'required|numeric|exists:council_session_items,id',
            'council_session_id' => 'required|numeric|exists:council_sessions,id',
            'status'=>'required|numeric',
            'user_id'=>'required|numeric',
        ],[
            'type_id.required' => 'Niste unijeli :attribute',
            'type_id.numeric' => ':attribute mora biti broj',
            'council_session_item_id.required' => 'Niste unijeli :attribute',
            'council_session_item_id.numeric' => ':attribute mora biti broj',
            'council_session_item_id.exists' => ':attribute ne postoji u bazi',
            'council_session_id.required' => 'Niste unijeli :attribute',
            'council_session_id.numeric' => ':attribute mora biti broj',
            'council_session_id.exists' => ':attribute ne postoji u bazi',
            'status.required' => 'Niste unijeli :attribute',
            'status.numeric' => ':attribute mora biti broj',
            'user_id.required' => 'Niste unijeli :attribute',
            'user_id.numeric' => ':attribute mora biti broj',

        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $council_session = new Notification($request->all());

        try{
            $council_session->saveOrFail();
            $message.='Uspješno unesena obavijest';

        } catch(\Exception $e) {
            return response()
                ->json([
                    'status' =>$e->getCode(),
                    'message'=> $e->getMessage()
                ]);
        }

        return response()
            ->json([
                'status' => Response::HTTP_OK,
                'message'=> $message
            ]);





    }


    public function getLast(){

        $notification = Notification::latest()->first();

        return response()
            ->json([
                'status' => Response::HTTP_OK,
                'notification'=> $notification
            ]);
    }
}
