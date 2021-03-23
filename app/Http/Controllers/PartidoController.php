<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Partido;
use App\Models\Equipo;
use App\Models\Liga;
use App\Models\User;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;

class PartidoController extends Controller
{
    //

      public function createPartido(Request $request)
    {

        $response = "";
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);

        //Si hay un json válido, crear el partido
        if($data){
            $partido = new Partido();
            $equipo1 = Equipo::where('nombre', $data->equipo1)->get()->first();
            $equipo2 = Equipo::where('nombre', $data->equipo2)->get()->first();

            if($equipo1->liga_id == $equipo2->liga_id){


                //TODO: Validar los data antes de guardar el partido
                $partido->equipo1_id = $equipo1->id;
                $partido->equipo2_id = $equipo2->id;
                $partido->liga_id = $equipo1->liga_id;
                $partido->fecha = $data->fecha;
                $partido->hora = $data->hora;

                try{
                    $partido->save();
                    $response = "OK";
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }
            }else{
                $response = "Error, equipos de ligas distintas";
            }
        }
        return response($response);
    }
     public function updatePartido(Request $request){

        $response = "";
        // Data for training classifier
        $samples = [[1, 3], [1, 4], [2, 4], [3, 1], [4, 1], [4, 2]];  // Training samples
        $labels = ['a', 'a', 'a', 'b', 'b', 'b'];
        // Initialize the classifier
        $classifier = new SVC(Kernel::LINEAR, $cost = 1000);
        // Train the classifier
        $classifier->train($samples, $labels);
        //Leer el contenido de la petición
        $data = $request->getContent();

        //Decodificar el json
        $data = json_decode($data);
        if($data){
        //Buscar el partido por su id
            $equipo1 = Equipo::where('nombre', $data->equipo1)->get()->first();
            $equipo2 = Equipo::where('nombre', $data->equipo2)->get()->first();
            $partido = Partido::where('fecha', $data->fecha)->where('equipo1_id', $equipo1->id)->where('equipo2_id',>            if($partido->resultadoEquipo1 || $partido->resultadoEquipo2 !== null){
                $response = "el partido ya tiene un resultado";

            }else{
            //Si hay un json válido, buscar el partido
                if($partido){
                //TODO: Validar los datos antes de guardar el partido

                    $partido->resultadoEquipo1 = $data->resultadoEquipo1;
                    $partido->resultadoEquipo2 = $data->resultadoEquipo2;

                    switch ($classifier->predict([$partido->resultadoEquipo1,$partido->resultadoEquipo2])) {
                       case 'a':
                       $equipo2->victorias += 1;
                       $equipo1->derrotas += 1;
                       $equipo1->save();
                       $equipo2->save();
                       break;
                       case 'b':
                       $equipo1->victorias += 1;
                       $equipo2->derrotas += 1;
                       $equipo1->save();
                       $equipo2->save();
                       break;
                   }

                   try{
                    $partido->save();
                    $response = "OK";
                }catch(\Exception $e){
                    $response = $e->getMessage();
                }
            }else{
                $response = "No partido";
            }
        }
    }else{
        $response = "datos incorrectos";
    }


    return response($response);
}

public function listaPartidosLec (){

    $response = "";
    $partidos = Partido::where('liga_id', 3)->orderBy('fecha')->orderBy('hora')->get();


    $response= [];

    foreach ($partidos as $partido) {

        $equipo1 = Equipo::find($partido->equipo1_id);
        $equipo2 = Equipo::find($partido->equipo2_id);
        $liga = Liga::find($equipo1->liga_id);

        $response[] = [
            "nombreEquipo1" => $equipo1->nombre,
            "nombreEquipo2" => $equipo2->nombre,
            "imagenEquipo1" => $equipo1->imagen,
            "imagenEquipo2" => $equipo2->imagen,
            "nombreLiga" => $liga->nombre,
            "imagenLiga" => $liga->imagen,
            "fecha" => $partido->fecha,
            "hora" => $partido->hora,
            "resultadoEquipo1"=> $partido->resultadoEquipo1,
            "resultadoEquipo2"=> $partido->resultadoEquipo2,

        ];
    }

    return response()->json($response);

}
public function listaPartidosLcs (){

    $response = "";
    $partidos = Partido::where('liga_id', 4)->orderBy('fecha')->orderBy('hora')->get();


    $response= [];

    foreach ($partidos as $partido) {

        $equipo1 = Equipo::find($partido->equipo1_id);
        $equipo2 = Equipo::find($partido->equipo2_id);
        $liga = Liga::find($equipo1->liga_id);

        $response[] = [
            "nombreEquipo1" => $equipo1->nombre,
            "nombreEquipo2" => $equipo2->nombre,
            "imagenEquipo1" => $equipo1->imagen,
            "imagenEquipo2" => $equipo2->imagen,
            "nombreLiga" => $liga->nombre,
            "imagenLiga" => $liga->imagen,
            "fecha" => $partido->fecha,
            "hora" => $partido->hora,
            "resultadoEquipo1"=> $partido->resultadoEquipo1,
            "resultadoEquipo2"=> $partido->resultadoEquipo2,

        ];
    }

    return response()->json($response);

}
public function proximoPartido (){
    $proximosPartidos = [];
    $response = "";
    $getHeaders = apache_request_headers ();
    $token = $getHeaders['Authorization'];
    $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";

    $decoded = JWT::decode($token, $key, array('HS256'));

    $user = User::where('nombre', $decoded)->get()->first();
    $equipo_id = $user->equipo_id;


    date_default_timezone_set("Europe/Madrid");
        //echo "The time is " . date("Y-m-d h:i");
    $fecha = date("Y-m-d");
    $hora = date("h:i");
        //Buscamos todos los partidos en los que participa el equipo asignado al usuario
    $partidos = Partido::where('equipo2_id', $equipo_id)->orWhere('equipo1_id', $equipo_id)->orderBy('fecha')->order>        //var_dump($partidos); exit();

    foreach ($partidos as $partido) {
      $fechaPartido = $partido->fecha;
      if ($fecha < $fechaPartido) {
        $proximosPartidos [] = $partido;
            //var_dump($proximosPartidos[0]); exit();
    }
}
$equipo1 = Equipo::find($proximosPartidos[0]->equipo1_id);
$equipo2 = Equipo::find($proximosPartidos[0]->equipo2_id);
$liga = Liga::find($equipo1->liga_id);
$response = [
    "nombreEquipo1" => $equipo1->nombre,
    "nombreEquipo2" => $equipo2->nombre,
    "imagenEquipo1" => $equipo1->imagen,
    "imagenEquipo2" => $equipo2->imagen,
    "nombreLiga" => $liga->nombre,
    "imagenLiga" => $liga->imagen,
    "fecha" => $proximosPartidos[0]->fecha,
    "hora" => $proximosPartidos[0]->hora
];
return response()->json($response);
}
public function partidosEquipo (){
    $response = [];
    $getHeaders = apache_request_headers ();
    $token = $getHeaders['Authorization'];
    $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";

    $decoded = JWT::decode($token, $key, array('HS256'));

    $user = User::where('nombre', $decoded)->get()->first();
    $equipo_id = $user->equipo_id;


    date_default_timezone_set("Europe/Madrid");
        //echo "The time is " . date("Y-m-d h:i");
    $fecha = date("Y-m-d");
    $hora = date("h:i");
        //Buscamos todos los partidos en los que participa el equipo asignado al usuario
    $partidos = Partido::where('equipo2_id', $equipo_id)->orWhere('equipo1_id', $equipo_id)->orderBy('fecha')->order>        //var_dump($partidos); exit();

    foreach ($partidos as $partido) {
     $equipo1 = Equipo::find($partido->equipo1_id);
     $equipo2 = Equipo::find($partido->equipo2_id);
     $liga = Liga::find($equipo1->liga_id);
     $response[] = [
        "nombreEquipo1" => $equipo1->nombre,
        "nombreEquipo2" => $equipo2->nombre,
        "imagenEquipo1" => $equipo1->imagen,
        "imagenEquipo2" => $equipo2->imagen,
        "nombreLiga" => $liga->nombre,
        "imagenLiga" => $liga->imagen,
        "fecha" => $partido->fecha,
        "hora" => $partido->hora,
        "resultadoEquipo1" => $partido->resultadoEquipo1,
        "resultadoEquipo2" => $partido->resultadoEquipo2,
    ];
}


return response()->json($response);
}

public function ultimoPartido (){
    $proximosPartidos = [];
    $response = "";
    $getHeaders = apache_request_headers ();
    $token = $getHeaders['Authorization'];
    $key = "kjsfdgiueqrbq39h9ht398erubvfubudfivlebruqergubi";

    $decoded = JWT::decode($token, $key, array('HS256'));

    $user = User::where('nombre', $decoded)->get()->first();
    $equipo_id = $user->equipo_id;


    date_default_timezone_set("Europe/Madrid");
        //echo "The time is " . date("Y-m-d h:i");
    $fecha = date("Y-m-d");
    $hora = date("h:i");
        //Buscamos todos los partidos en los que participa el equipo asignado al usuario
    $partidos = Partido::where('equipo2_id', $equipo_id)->orWhere('equipo1_id', $equipo_id)->orderBy('fecha', 'desc'>        //var_dump($partidos); exit();

    foreach ($partidos as $partido) {
      $fechaPartido = $partido->fecha;
      if ($fecha > $fechaPartido) {
        $proximosPartidos [] = $partido;
            //var_dump($proximosPartidos[0]); exit();
    }
}
$equipo1 = Equipo::find($proximosPartidos[0]->equipo1_id);
$equipo2 = Equipo::find($proximosPartidos[0]->equipo2_id);
$liga = Liga::find($equipo1->liga_id);
$resultado2in = $proximosPartidos[0]->resultadoEquipo2;
$resultado2 = "$resultado2in";
$resultado1in = $proximosPartidos[0]->resultadoEquipo1;
$resultado1 = "$resultado1in";
$response = [
    "nombreEquipo1" => $equipo1->nombre,
    "nombreEquipo2" => $equipo2->nombre,
    "imagenEquipo1" => $equipo1->imagen,
    "imagenEquipo2" => $equipo2->imagen,
    "nombreLiga" => $liga->nombre,
    "imagenLiga" => $liga->imagen,
    "fecha" => $proximosPartidos[0]->fecha,
    "hora" => $proximosPartidos[0]->hora,
    "resultadoEquipo1" => $resultado1,
    "resultadoEquipo2" => $resultado2,
];
return response()->json($response);
}

public function clasificatoriaLec (){

 $response= [];
        //Buscamos todos los equipo y se ordenan por liga y victorias
 $equipos = Equipo::where('liga_id', 3)->orderBy('victorias', 'desc')->get();
        //var_dump($partidos); exit();
        //var_dump($equipos);exit();

 foreach ($equipos as $equipo) {
    $liga = Liga::find($equipo->liga_id);
    $response[]= [
        "nombre" => $equipo->nombre,
        "imagen" => $equipo->imagen,
        "nombreLiga" => $liga->nombre,
        "imagenLiga" => $liga->imagen,
        "victorias"=>$equipo->victorias,
        "derrotas"=>$equipo->derrotas,
    ];

}
return response()->json($response);


}
public function clasificatoriaLcs (){

 $response= [];
        //Buscamos todos los equipo y se ordenan por liga y victorias
 $equipos = Equipo::where('liga_id', 4)->orderBy('victorias', 'desc')->get();
        //var_dump($partidos); exit();
        //var_dump($equipos);exit();

 foreach ($equipos as $equipo) {
    $liga = Liga::find($equipo->liga_id);
    $response[]= [
        "nombre" => $equipo->nombre,
        "imagen" => $equipo->imagen,
        "nombreLiga" => $liga->nombre,
        "imagenLiga" => $liga->imagen,
        "victorias"=>$equipo->victorias,
        "derrotas"=>$equipo->derrotas,
    ];

}

return response()->json($response);


}
}