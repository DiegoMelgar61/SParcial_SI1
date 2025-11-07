<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Config;

//ENDPOINT LOGIN
Route::match(['get','post'],'/login',function(Request $request)
{
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');

    //SI ES UNA SOLICITUD GET REDIRIGIR A TEMPLATE LOGIN
    if ($request->isMethod('get'))
    {
        return view('login');
    }

    //OBTENER DATOS
    $data=$request->json()->all();
    $codigo=$data['codigo'];
    $password=$data['password'];
    $accion='INICIO DE SESION';
    $estado='ERROR';
    $fecha=date('Y-m-d H:i:s');
    $comentario='Registro de inicio de Sesion';
    $db=Config::$db;

    if (!$codigo || !$password)
    {
        $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);
        return response()->json([
            'success'=>false,
            'message'=>'Debe enviar todos los datos'
        ],401);
    }
    try
    {
        $db->create_conection();

        //VALIDACION:VERIFICAR SI EL USUARIO CON EL CODIGO INGRESADO EXISTE
        $sql="
            SELECT u.codigo,u.password_hash,r.nombre as rol
            FROM ex_g32.usuario u
            INNER JOIN ex_g32.rol r ON r.id=u.id_rol
            WHERE codigo= :codigo
        ";
        $params=[
            ':codigo'=>$codigo
        ];
        $stmt=$db->execute_query($sql,$params);
        
        $usuario=$db->fetch_one($stmt);

        if (!$usuario)
        {
            $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);
            return response()->json([
                'success'=>false,
                'message'=>'El usuario no existe.'
            ],401);    
        }
        //VALIDAR CONTRASEÑA
        if (!password_verify($password,$usuario['password_hash']))
        {
            $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);
            return response()->json([
                'success'=>false,
                'message'=>'Contraseña incorrecta.'
            ],401);
        }

        //SI LA CONTRASEÑA ES CORRECTA GUARDAR SESION
        Session::put('user_code',$usuario['codigo']);
        Session::put('user_role',$usuario['rol']);
        $estado='SUCCESS';

        $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);
        return response()->json([
            'success'=>true,
            'message'=>'Inicio de Sesion exitoso.'
        ]);
    }
    catch (Exception $e)
    {
        $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);
        return response()->json([
            'success'=>false,
            'message'=>'Error al Iniciar Sesion.',
            'error'=>$e->getMessage()
        ],500);
    }
    finally
    {
        if (isset($db) && $db !== null) 
        {
            $db->close_conection();
        }
    }
});

//ENDPOINT LOGOUT
Route::post('/logout',function()
{
    //INICIAR VARIABLE GESTORA DE DB
    $db=Config::$db;
    try
    {
        //ABRIR CONEXION A LA BASE DE DATOS
        $db->create_conection();

        //CAPTURAR PARAMETROS DE REGISTRO EN BITACORA
        $accion='CERRAR SESION';
        $codigo=Session::get('user_code');
        $estado='SUCCESS';
        $fecha=date('Y-m-d H:i:s');
        $comentario='Registro de Cierre de Sesion';

        $db->save_log_bitacora($accion,$fecha,$estado,$comentario,$codigo);

        //QUITAR USUARIO DE LA SESION
        Session::flush();
        return redirect('/login');
    }
    catch(Exception $e)
    {
        //SI FALLA EXPULSAR DE LA SESION Y REDIRIGIR AL LOGIN
        Session::flush();
        return redirect('/login');
    }
    finally
    {
        if (isset($db) && $db !== null) 
        {
            $db->close_conection();
        }
    }
});