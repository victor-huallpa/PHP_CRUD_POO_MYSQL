<?php
	require_once "../../config/app.php";
	require_once "../views/inc/session_start.php";
	require_once "../../autoload.php";
	
	use app\controllers\categoryController;

	if(isset($_POST['modulo_categoria'])){

		$insUsuario = new categoryController();

		if($_POST['modulo_categoria']=="registrar"){
			echo $insUsuario->registrarCategoriaControlador();
		}

		if($_POST['modulo_categoria']=="eliminar"){
			echo $insUsuario->eliminarCategoriaControlador();
		}

		if($_POST['modulo_categoria']=="actualizar"){
			echo $insUsuario->actualizarCategoriaControlador();
		}
		
	}else{
		session_destroy();
		header("Location: ".APP_URL."login/");
	}