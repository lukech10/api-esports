<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Liga;





class LigaController extends Controller
{
    //

    public function createLiga(Request $request)
    {
        
        $response = "";
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        //Si hay un json válido, crear el liga
        if($data){
            $liga = new Liga();

            //TODO: Validar los data antes de guardar el liga
            
            $liga->nombre = $data->nombre;
            $liga->imagen = $data->imagen;
            

            
            try{
                $liga->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }

            
        }

        
        return response($response);
    }

    public function listaLigas(){

        $response = "";
        $ligas = Liga::get();

        $response= [];

        foreach ($ligas as $liga) {
            $response[] = [
                
                "id" => $liga->id,
                "url" => $liga->imagen
                
            ];
        }
        


        return response()->json($response);
    }
}