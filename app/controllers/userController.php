<?php
    namespace app\controllers;
    use app\models\mainModel;

    class userController extends mainModel{

        #metodo de registar un usuario
        public function regsitrarUsuarioControlador(){
            #almacenar los datos del usuario
            $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
            $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
            $usuairo = $this->limpiarCadena($_POST['usuario_usuario']);
            $email = $this->limpiarCadena($_POST['usuario_email']);
            $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
            $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

            #verificando campos obligatorios
            if($nombre == '' || $apellido == '' || $usuairo == '' || $clave1 == '' || $clave2 == ''){
                $alerta = [
                    "tipo" => 'simple',
                    "titulo" => "Ocurrio un error Inerperado",
                    "texto" => "No as llenado todo los campo que son obligatorios",
                    "icono" => 'error'
                ];

                return json_encode($alerta);
                exit();
            }

            #verificar la integridad de los datos
            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)){
                $alerta = [
                    "tipo" => 'simple',
                    "titulo" => "Ocurrio un error Inerperado",
                    "texto" => "El nombre no coinside con los caracteres solicitados",
                    "icono" => 'error'
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)){
                $alerta = [
                    "tipo" => 'simple',
                    "titulo" => "Ocurrio un error Inerperado",
                    "texto" => "El apellido no coinside con los caracteres solicitados",
                    "icono" => 'error'
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuairo)){
                $alerta = [
                    "tipo" => 'simple',
                    "titulo" => "Ocurrio un error Inerperado",
                    "texto" => "El nombre de usuario no coinside con los caracteres solicitados",
                    "icono" => 'error'
                ];

                return json_encode($alerta);
                exit();
            }

            if($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)){
                $alerta = [
                    "tipo" => 'simple',
                    "titulo" => "Ocurrio un error Inerperado",
                    "texto" => "Las claves no coinside con los caracteres solicitados",
                    "icono" => 'error'
                ];

                return json_encode($alerta);
                exit();
            }
        }
    }