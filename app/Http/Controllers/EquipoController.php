<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Equipo;
use App\Models\User;





class EquipoController extends Controller
{
    //

    public function createEquipo(Request $request)
    {
        
        $response = "";
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        //Si hay un json válido, crear el equipo
        if($data){
            $equipo = new Equipo();

            //TODO: Validar los data antes de guardar el equipo
            $equipo->nombre = $data->nombre;
            $equipo->imagen = $data->imagen;
            $equipo->liga_id = $data->liga_id;
            $equipo->victorias = $data->victorias;
            $equipo->derrotas = $data->derrotas;
            
            try{
                $equipo->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }
        }    
        return response($response);
    }

    public function listaEquipos(Request $request)
    {

        $response = "";

        $data = $request->getContent();
        $data = json_decode($data);

        $equipos = Equipo::get();
        
        
            $response= [];

            foreach ($equipos as $equipo) {

               $equipo = Equipo::where('liga_id', $data->liga_id)->get();
               var_dump($equipo); exit();

               for ($i=0; $i < count($equipo) ; $i++) { 
                   $response[$i] = [
                    "id" => $equipo[$i]->id,
                    "imagen" => $equipo[$i]->imagen,
               ];
               }
              
            }
        
         return response()->json($response);

    }

    public function elegirEquipo(Request $request)
    {
        $response = "";
        $getHeaders = apache_request_headers ();
        $token = $getHeaders['Authorization'];
        $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";
        
            $decoded = JWT::decode($token, $key, array('HS256'));
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        if ($data){
           if($equipo = Equipo::find($data->equipo_id)){
            $user = User::where('nombre', $decoded)->get()->first();

            $user->equipo_id = $data->equipo_id;
              try{
                $user->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }
        }else{
            $response = "equipo no encontrado"; 
        }
        } 
         return response($response);
    }
  
}