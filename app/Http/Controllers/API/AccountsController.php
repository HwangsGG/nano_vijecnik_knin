<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Validator;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function getUsers(Request $request){

        $validator = Validator::make($request->all(), [
            'account_id'=>'required|numeric',

        ],[
            'account_id.required' => 'Niste unijeli :attribute',
            'account_id.numeric' => ':attribute mora biti broj',
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'errors' =>$validator->errors(),
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ]);
        }

        $account = Account::where('id',$request['account_id'])
            ->with('users','users.role')
            ->withCount('users')
            ->first();

        //return $account;
        if($account){
            return response()
                ->json([
                    'count' =>$account['users_count'],
                    'users' => $account['users'],
                    'status' => Response::HTTP_OK
                ]);
        }else{
            return response()
                ->json([
                    'message' =>'id ne postoji',
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY

                ]);
        }


    }


}
