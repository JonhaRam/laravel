<?php

namespace App\Http\Controllers;

use App\SpotsTypes;
use App\Spots;
use Illuminate\Http\Request;

class SpotsController extends Controller
{

    public function index(Request $request)
    {
        try {
            if($request->query() && $request->query('spot_type')) {
                $spots = Spots::with('spotType:id,name')->where('spot_type_id', $request->query('spot_type'))->get();
            } else {
                $spots = Spots::with('spotType:id,name')->get();
            }
            $spots->makeHidden('spot_type_id');
            $data = array('spots' => $spots);
            $success = true;
            $statusCode = 200;
        } catch (\Throwable $t) {
            $data = array('message' => 'Ha ocurrido un error en la solicitud ' . $t);
            $success = false;
            $statusCode = 500;
        }

        // EN EL CASO DEL LISTADO DE LUGARES, LOS RESULTADOS PUEDEN SER PAGINADOS Y ALMACENADOS EN CACHE
        return response()->json(array(
            'success' => $success,
            'data' => $data
        ), $statusCode);
    }

    public function store(Request $request)
    {
        $spotData = $request->input();

        $spotType = SpotsTypes::where('id', $spotData['spot_type_id'])->first();
        if ($spotType) {

            // TODO: ¿QUÉ VALIDACIONES SE TIENEN QUE APLICAR SOBRE LA INFORMACIÓN?
            $spot = new Spots();
            $spot->name = $spotData['name'];
            $spot->image = $spotData['image'];
            $spot->number = $spotData['number'];
            $spot->street = $spotData['street'];
            $spot->zip_code = $spotData['zip_code'];
            $spot->lat = $spotData['lat'];
            $spot->lng = $spotData['lng'];

            $spot->spotType()->associate($spotType)->save();

            $data = array('spot' => $spot);
            $success = true;
        } else {
            $data = array('message' => 'El tipo de lugar no existe');
            $success = false;
        }

        return response()->json(array(
            'success' => $success,
            'data' => $data
        ));
    }

    public function show($id)
    {
        $existsSpot = Spots::with('spotType:id,name')->where('id', $id)->get()->first();
        if ($existsSpot) {
            $existsSpot->makeHidden('spot_type_id');
            $response = response()->json(array(
                'success' => true,
                'data' => array('spot' => $existsSpot)
            ));
        } else {
            $response = response(null, 404);
        }

        return $response;
    }

    public function update(Request $request, $id)
    {
        $existsSpot = Spots::where('id', $id)->first();
        if ($existsSpot) {
            $spotData = $request->input();
            $spotType = SpotsTypes::where('id', $spotData['spot_type_id'])->first();
            if ($spotType) {

                $existsSpot->name = $spotData['name'];
                $existsSpot->image = $spotData['image'];
                $existsSpot->number = $spotData['number'];
                $existsSpot->street = $spotData['street'];
                $existsSpot->zip_code = $spotData['zip_code'];
                $existsSpot->lat = $spotData['lat'];
                $existsSpot->lng = $spotData['lng'];

                $existsSpot->spotType()->associate($spotType)->save();

                $data = array('spot' => $existsSpot);
                $success = true;
            } else {
                $data = array('message' => 'El tipo de lugar no existe');
                $success = false;
            }

            $response = response()->json(array(
                'success' => $success,
                'data' => $data
            ));
        } else {
            $response = response(null, 404);
        }

        return $response;
    }

    public function destroy($id)
    {
        $existsSpot = Spots::where('id', $id)->first();
        if ($existsSpot) {
            try {
                $existsSpot->delete();
                // EL ESTATUS DE LA PETICIÓN PUEDE ENVIARSE COMO UN 200 ó 204
                $response = response()->json(array(
                    'success' => true,
                    'data' => array('message' => 'Lugar eliminado correctamente')
                ));
            } catch (\Throwable $t) {
                $response = response()->json(array(
                    'success' => false,
                    'data' => array('message' => 'No se ha podido eliminar el lugar. ' . $t)
                ), 500);
            }
        } else {
            $response = response(null, 404);
        }

        return $response;
    }
}
