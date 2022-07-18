<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;

class ARController extends Controller
{
    public function index(){
        $markers= Marker::where('type','text')->get();
        $video_markers= Marker::where('type','video')->get();
        $image_markers =Marker::where('type','picture')->get();
        $model_markers= Marker::where('type','models')->get();

        return view('ar.ar',compact('markers','video_markers','image_markers','model_markers'));
    }


    public function test(){
        $model_markers= Marker::where('type','models')->get();
        return view('ar.test',compact('model_markers'));
    }

    public function falcon(){

        return view('ar.falcon');
    }
}
