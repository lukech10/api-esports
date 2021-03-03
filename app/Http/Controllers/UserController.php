<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\RecuperarContrasena;
use Illuminate\Support\Facades\Mail;




class UserController extends Controller
{
    //

    public function createUser(Request $request)
    {
        
        $response = "";
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        //Si hay un json válido, crear el user
        if($data){
            $user = new User();

            //TODO: Validar los data antes de guardar el user
            
            $user->nombre = $data->nombre;
            $user->email = $data->email;
            $user->password = Hash::make($data->password);
           

            
            try{
                $user->save();
                $response = "OK";
            }catch(\Exception $e){
                $response = $e->getMessage();
            }

            
        }

        
        return response($response);
    }

    public function Login(Request $request){
        $respuesta = "";

        //Procesar los data recibidos
        $data = $request->getContent();

        //Verificar que hay data
        $data = json_decode($data);

        if($data){
            
            if(isset($data->nombre)&&isset($data->password)){

                $user = User::where("nombre",$data->nombre)->first();

                if($user){

                    if(Hash::check($data->password, $user->password)){
                    
                        $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";
                      
                        $token = JWT::encode($data->nombre, $key);

                        $user->api_token = $token;

                        $user->save();

                        $respuesta = $token;

                    }else{
                        $respuesta = "Contraseña incorrecta";
                    }

                }else{
                    $respuesta = "Usuario no encontrado";
                }

            }else{
                $respuesta = "Faltan data";
            }

        }else{
            $respuesta = "data incorrectos";
        }
        


        return response($respuesta);
    }

    public function resetPassword(Request $request){
    
        $respuesta = "";

        //Procesar los data recibidos
        $data = $request->getContent();

        //Verificar que hay data
        $data = json_decode($data);

        if($data){
            
            if(isset($data->email)){

                $user = User::where("email",$data->email)->first();

                if($user){

                    if($data->email == $user->email){
                        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

                        $password = substr(str_shuffle($permitted_chars), 0, 10);
                        $user->password = $password;
                        $user->save();
                        
                        //var_dump($user); exit;
                        $rc = new RecuperarContrasena($user);
                        // var_dump($rc); exit;

                        Mail::to($data->email)->send($rc);

                        $user->password = Hash::make($password);
                         try{
                            $user->save();
                            $response = "OK";
                        }catch(\Exception $e){
                            $response = $e->getMessage();
                        }
                        $respuesta ="revisa tu correo.";

                    }else{
                        $respuesta = "email incorrecta";
                    }

                }else{
                    $respuesta = "Usuario no encontrado";
                }

            }else{
                $respuesta = "Faltan data";
            }

        }else{
            $respuesta = "data incorrectos";
        }
        


        return response($respuesta);
    }
    public function changePassword(Request $request){
    
        $respuesta = "";

        //Procesar los data recibidos
        $data = $request->getContent();

        //Verificar que hay data
        $data = json_decode($data);

        if($data){
            
            if(isset($data->password)){

                $user = User::where("password",Hash::check($data->password, $user->password))->first();

                if($user){

                    if($data->email == $user->email){
                       
                        $user->password = $data->newpassword;
                        $user->password = Hash::make($password);

                         try{
                            $user->save();
                            $response = "OK";
                        }catch(\Exception $e){
                            $response = $e->getMessage();
                        }
                        $respuesta ="revisa tu correo.";

                    }else{
                        $respuesta = "email incorrecta";
                    }

        return response($respuesta);
    }
            }
        }
    }
    public function mostrarDatos(){
        $response = "";
        $getHeaders = apache_request_headers ();
        $token = $getHeaders['Authorization'];
        $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";
        
        $decoded = JWT::decode($token, $key, array('HS256'));
        
        $user = User::where('nombre', $decoded)->get()->first();

        $response = [
            "email" => $user->email,
            "nombre" => $user->nombre
        ];

        return response()->json($response);
    }
}
