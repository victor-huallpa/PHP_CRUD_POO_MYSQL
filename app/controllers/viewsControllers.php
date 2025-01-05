<?php
    namespace app\controllers;
    use app\models\viewsModels;

    class viewsControllers extends viewsModels{

        #metodos
        public function obtenerVistacontrolador($vista){
            if($vista != ""){
                $respuesta = $this->obtenerVistaModelo($vista);
            }else{
                $respuesta = "login";
            }

            return $respuesta;
        }

    }