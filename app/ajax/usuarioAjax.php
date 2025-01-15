<?php
    require_once '../../config/app.php';
    require_once '../views/inc/session_start.php';
    require_once '../../autoload.php';

    use app\controllers\userController;

    if(isset($_POST['modulo_usuario'])){
        $inUsuario = new userController();

        if($_POST['modulo_usuario'] == 'registrar'){
            echo $inUsuario->regsitrarUsuarioControlador();
        }else{}
    }else{
        session_destroy();
        header('Location: '.APP_URL.'login/');
    }