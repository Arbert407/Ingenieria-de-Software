<?php

namespace App\Http\Controllers;

use App\Personal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //Importar DB
use Illuminate\Support\Facades\Crypt; //Encriptar/Desencriptar contraseñas
use Illuminate\Support\Facades\Hash;  //Manejo de hashes

class MainController extends Controller
{




    public function index()
    {
        return view('iniciarSesion');
    }




    public function finalizar()
    {
        return view('finEncuesta');
    }




    public function paginaLaravel()
    {
        return view('welcome');
    }




    public function ingresarComoAdmin()
    {
        return view('principalAdmin');
    }




    public function preguntar()
    {
        return view('preguntaFiltro');
    }




    public function verificar()
    {


        //Valida la informacion entrante, de no cumplirse las reglas
        //regresa a la pagina de iniciar sesion con los errores
        $data = request()->validate([
            'numeroEmpleado' => ['required','exists:personal,no_empleado'], //verifica si existe el numero de empleado en la tabla personal, en la columna no_empleado
            'contrasena' => ['required']
        ],[
            'numeroEmpleado.required' => 'El número de empleado es obligatorio',
            'numeroEmpleado.exists' => 'El número de empleado es invalido',
            'contrasena.required' => 'La contraseña es obligatoria',
        ]);

        /**
         * 
         * Metodo 1: Usando cifrado (encrypt/decrypt)
         * 
         *  */
        //Busca la contraseña perteneciente al numero de empleado y la desencripta
        //se almacena en la variable $pass
        $usuario = DB::table('personal')
                    ->where('no_empleado',$data['numeroEmpleado'])
                    ->first();
        $pass = Crypt::decryptString($usuario->contrasena);
        //Compara la contraseña ingresada con la obtenida en la consulta, si son iguales
        //entonces pasa a la pregunta filtro, sino, entonces regresa a iniciar sesion
        //mostrando el error de contraseña incorrecta
        if($pass != $data['contrasena'])
        {
            return back()->withErrors([
                'contrasena'=>'La contraseña es incorrecta',
            ])->withInput();
        }else
        {
            //Almacenar la hora de entrada al sistema
            DB::select('call Guardar_reg_entrada(?,?,?,?,?)',
            array(
                $usuario->nombres,
                substr(env('APP_KEY'),57),
                Hash::make($usuario->contrasena),
                $usuario->email,
                $usuario->activo,
                //strval(date('ymdHi')),
                //strval(date('ymdHi')),
            ));

            $posicion = $usuario->id_posicion;
            if($posicion == 1)
            {
                return redirect()->route('pagina.principalAdmin');
            }
            return redirect()->route('encuesta.preguntaFiltro');   
        } 
        
        /**
         * 
         * Metodo 2: Usando hashes
         * 
         *  */
        /*
        //Busca la contraseña perteneciente al numero de empleado
        $usuario = DB::table('personal')
                    ->where('no_empleado',$data['numeroEmpleado'])
                    ->first();
        //Crea un hash con los datos ingresados 
        //si los hashes coinciden entonces la contraseña es correcta
        if(Hash::check($data['contrasena'],$usuario->contrasena))
        {
            //dd($usuario->contrasena);
            $posicion = $usuario->id_posicion;
            if($posicion == 1)
            {
                return redirect()->route('pagina.principalAdmin');
            }
            return redirect()->route('encuesta.preguntaFiltro');   
        }else
        {
            //dd($data['contrasena']);
            return back()->withErrors([
                'contrasena'=>'La contraseña es incorrecta',
            ])->withInput();
        }    
        */

        
    }  




}
