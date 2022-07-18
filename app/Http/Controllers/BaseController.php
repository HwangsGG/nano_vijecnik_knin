<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $name="";
    protected $last_name="";
    protected $email="";
    protected $id="";
    protected $pageOptions = [50, 100, 150,200, 500];
    protected $user="";

    /*public function __construct()
    {
      $user = auth('api')->user();
      $this->user=$user;
      $this->name=$user->name;
      $this->last_name=$user->last_name;
      $this->email=$user->email;
      $this->id=$user->id;
    }*/

    /**
     * success response method.
     *
     * @param $result
     * @param $message
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendResponseError($result, $message)
    {
        $response = [
            'success' => false,
            'data'    => $result,
            'message' => $message,
        ];


        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     *
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }


    /**
     * return Unauthorized response.
     *
     * @param $error
     * @param  int  $code
     *
     * @return JsonResponse
     */
    public function unauthorizedResponse($error = 'Forbidden', $code = 403)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
