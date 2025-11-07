<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Config;

//RUTA PRINCIPAL (INDEX)
Route::get('/', function () 
{
    //VALIDACION: SI EL USUARIO NO ESTA REGISTRADO REDIRIGIR AL LOGIN
    if (!Session::has('user_code'))
    {
        return redirect('/login');
    }
    
    //OBTENER DATOS DEL USUARIO
    $db=Config::$db;
    $db->create_conection();
    $sql="
        SELECT u.codigo,u.ci,p.nomb_comp,p.tel,p.correo,r.nombre as rol
        FROM ex_g32.usuario u
        INNER JOIN ex_g32.persona p ON p.ci=u.ci
        INNER JOIN ex_g32.rol r ON r.id =u.id_rol
        WHERE u.codigo= :codigo
    ";
    $params=[
        ':codigo'=>Session::get('user_code')
    ];

    $stmt=$db->execute_query($sql,$params);
    $user=$db->fetch_one($stmt);
    Session::put('name',$user['nomb_comp']);
    Session::put('mail',$user['correo']);
    Session::put('tel',$user['tel']);
    Session::put('ci',$user['ci']);
    $db->close_conection();

    return view('index',['user'=>$user]);
});
