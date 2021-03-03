<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use App\Models\Noticia;
use App\Models\Equipo;
use App\Models\Liga;
use App\Models\User;

/**
 * 
 */
class NoticiaController extends Controller
{
	
	public function createNoticia(Request $request)
	{

		$response = "";
        //Leer el contenido de la petición
		$data = $request->getContent();

        //Decodificar el json
		$data = json_decode($data);

        //Si hay un json válido, crear el noticia
		if($data){
			$noticia = new Noticia();
			if(isset($data->equipo)) {
				$equipo = Equipo::where('nombre', $data->equipo)->get()->first();
				$noticia->equipo_id = $equipo->id;
			}
			$liga = Liga::where('nombre', $data->liga)->get()->first();
			$noticia->liga_id = $liga->id;
			$noticia->encabezado = $data->encabezado;
			$noticia->fecha = $data->fecha;
			$noticia->imagen = $data->imagen;
			$noticia->url = $data->url;

			try{
				$noticia->save();
				$response = "OK";
			}catch(\Exception $e){
				$response = $e->getMessage();
			}
		}

		return response($response);
	}

	public function listaNoticias(){

        $response = "";
        $noticias = Noticia::orderBy('fecha', 'desc')->get();

        $response= [];

        foreach ($noticias as $noticia) {
            $response[] = [
                
                
                "url" => $noticia->url,
                "encabezado" => $noticia->encabezado,
                "imagen" => $noticia->imagen,
                "fecha" => $noticia->fecha

                
            ];
        }
        


        return response()->json($response);
    }
    public function listaFiltrada(){
        $proximosnoticias = [];
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
        //Buscamos todos los noticias en los que participa el equipo asignado al usuario
        $noticias = Noticia::where('equipo_id', $equipo_id)->orderBy('fecha', 'desc')->get();
        //var_dump($noticias); exit();
        foreach ($noticias as $noticia) {
        $response = [
              
                "url" => $noticia->url,
                "encabezado" => $noticia->encabezado,
                "imagen" => $noticia->imagen,
                "fecha" => $noticia->fecha,
        ];
    }
        return response()->json($response);
    }
	
}

?>