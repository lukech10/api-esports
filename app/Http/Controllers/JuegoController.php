<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Juego;





class JuegoController extends Controller
{
    //

    public function createJuego(Request $request)
    {
        
        $response = "";
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        //Si hay un json válido, crear el Juego
        if($data){
            $Juego = new Juego();

            //TODO: Validar los data antes de guardar el Juego
            
            $Juego->nombre = $data->nombre;
            $Juego->imagen = $data->imagen;
            

            
            try{
                $Juego->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }

            
        }

        
        return response($response);
    }

    public function listaJuegos(){

        $response = "";
        $juegos = Juego::get();

        $response= [];

        foreach ($juegos as $juego) {
            $response[] = [
                
                "url" => $juego->imagen
                
            ];
        }
        


        return response()->json($response);
    }
}