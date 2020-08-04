<?php

namespace App\Http\Controllers;

use App\SpotsTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpotsTypesController extends Controller
{

    public function index()
    {
        try {
            $spotsTypes = SpotsTypes::all('id', 'name');
            $data = array('spots_types' => $spotsTypes);
            $success = true;
            $statusCode = 200;
        } catch (\Throwable $t) {
            // CON BASE EN LOS DISTINTOS ERRORES QUE SE PUEDEN PRESENTAR EN LA SOLICITUD SE OBTIENE LOS DETALLES Y SE
            // INFORMA A LA CAPA DE PRESENTACIÓN PARA SU MANEJO
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

    /**
     * Obtiene el Id y Nombre de los tipos de espacios disponibles, utilizado para llenar dropdowns
     * @return \Illuminate\Http\JsonResponse
     */
    public function opciones() {
        try {
            $spotsTypes = SpotsTypes::all(['id', 'name']);
            $data = array('spots_types' => $spotsTypes);
            $success = true;
        } catch (\Throwable $t) {
            $data = array('message' => 'Ha ocurrido un error en la solicitud. ' . $t);
            $success = false;
        }

        return response()->json(array(
            'success' => $success,
            'data' => $data
        ));
    }

    public function store(Request $request)
    {
        $spotTypeData = $request->input();

        $existsSpotType = SpotsTypes::where('name', $spotTypeData['name'])->first();
        if ($existsSpotType) {
            $data = array('message' => 'Ya existe un tipo de lugar con ese nombre');
            $success = false;
        } else {
            $spotType = new SpotsTypes();
            $spotType->name = $spotTypeData['name'];
            $spotType->save();

            // CON BASE EN LAS NECESIDADES DE LA CAPA DE PRESENTACIÓN, SE PUEDE DEVOLVER LA INFORMACIÓN DEL ELEMENTO
            // CREADO O UN MENSAJE CON EL RESULTADO DE LA OPERACIÓN
            $data = array('spot_type' => $spotType);
            $success = true;
        }

        return response()->json(array(
            'success' => $success,
            'data' => $data
        ));
    }

    public function show($id)
    {
        $existsSpotType = SpotsTypes::where('id', $id)->first();
        if ($existsSpotType) {
            $response = response()->json(array(
                'success' => true,
                'data' => array('spot_type' => $existsSpotType)
            ));
        } else {
            $response = response(null, 404);
        }

        return $response;
    }

    public function update(Request $request, $id)
    {
        $existsSpotType = SpotsTypes::where('id', $id)->first();
        if ($existsSpotType) {

            $spotTypeData = $request->input();

            $repeatedSpotType = SpotsTypes::where('name', $spotTypeData['name'])->first();
            if ($repeatedSpotType && $existsSpotType != $repeatedSpotType) {
                $data = array('message' => 'Ya existe un tipo de lugar con ese nombre');
                $success = false;
            } else {
                $existsSpotType->name = $spotTypeData['name'];
                $existsSpotType->save();

                $data = array('spot_type' => $existsSpotType);
                $success = true;
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
        $existsSpotType = SpotsTypes::where('id', $id)->first();
        if ($existsSpotType) {
            try {
                $existsSpotType->spots()->delete();
                $existsSpotType->delete();

                // EL ESTATUS DE LA PETICIÓN PUEDE ENVIARSE COMO UN 200 ó 204
                $response = response()->json(array(
                    'success' => true,
                    'data' => array('message' => 'Tipo de lugar eliminado correctamente')
                ));
            } catch (\Throwable $t) {
                $response = response()->json(array(
                    'success' => false,
                    'data' => array('message' => 'No se ha podido eliminar el tipo de lugar. ' . $t)
                ), 500);
            }
        } else {
            $response = response(null, 404);
        }

        return $response;
    }
}
