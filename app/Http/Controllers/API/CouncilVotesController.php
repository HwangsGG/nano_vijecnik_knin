<?php

namespace App\Http\Controllers\api;

use App\Events\CouncilSession;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CouncilVote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class CouncilVotesController extends Controller
{
    public function getVotes(Request $request){

        $validator = Validator::make($request->all(), [
            'council_session_item_id'=>'required|numeric|exists:council_session_items,id',
            'council_session_id' => 'required|numeric|exists:council_sessions,id'

        ],[
            'council_session_item_id.required' => 'Niste unijeli :attribute',
            'council_session_item_id.numeric' => ':attribute mora biti broj',
            'council_session_item_id.exists' => ':attribute ne postoji u bazi',
            'council_session_id.required' => 'Niste unijeli :attribute',
            'council_session_id.numeric' => ':attribute mora biti broj',
            'council_session_id.exists' => ':attribute ne postoji u bazi',

        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $votes = CouncilVote::where('council_session_id',$request['council_session_id'])
            ->where('council_session_item_id',$request['council_session_item_id'])
            ->with('council_session','council_session_item','user','vote_type')
            ->get();

        //return $account;
        if($votes){
            event(new CouncilSession('Dohvat glasova'));
            return response()
                ->json([
                    'council_votes' => $votes,
                    'status' => Response::HTTP_OK
                ]);
        }else{

            return response()
                ->json([
                    'message' =>'Ne postoje glasovi za te parametre',
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY

                ]);
        }


    }
}
