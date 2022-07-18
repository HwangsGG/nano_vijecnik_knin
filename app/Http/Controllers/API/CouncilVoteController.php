<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CouncilVote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class CouncilVoteController extends Controller
{
    public function store(Request $request){


        $validator = Validator::make($request->all(), [
            'council_session_id'=>'required|numeric',
            'council_session_item_id'=>'required|numeric',
            'council_session_item_type_id'=> 'required|numeric',
            'user_id'=>'required|numeric',
            'vote_type_id'=>'required|numeric',

        ],[
            'council_session_id.required' => 'Niste unijeli :attribute',
            'council_session_id.numeric' => ':attribute mora biti broj',
            'council_session_item_id.required' => 'Niste unijeli :attribute',
            'council_session_item_id.numeric' => ':attribute mora biti broj',
            'council_session_item_type_id.required' => 'Niste unijeli :attribute',
            'council_session_item_type_id.numeric' => ':attribute mora biti broj',
            'user_id.required' => 'Niste unijeli :attribute',
            'user_id.numeric' => ':attribute mora biti broj',
            'vote_type_id.required' => 'Niste unijeli :attribute',
            'vote_type_id.numeric' => ':attribute mora biti broj',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }
        $councilVote = new CouncilVote($request->all());

        /*$councilVote = CouncilVote::create([
            'name' => 'London to Paris',
        ]);*/

        try{
            $councilVote->saveOrFail();

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
