<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Marker;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use PDF;

class ExportController extends Controller
{

    public function generatePDF(Group $group)
    {
        $markers = $group->markers->toArray();
        $data = compact('markers');
       // Log::alert(json_encode($markers));
        //$data = ['title' => 'Export picture marker'];
        $pdf = PDF::loadView('export/exportPicturesMarker',$data);

        return $pdf->download('markers.pdf');
    }


    public function generatePdfWithText(Marker $marker)
    {
        $name = $marker->name;
        $marker = $marker->toArray();
        $data = compact('marker');

        $pdf = PDF::loadView('export/exportTextMarker',$data);

        return $pdf->download($name.'.pdf');
    }

}
