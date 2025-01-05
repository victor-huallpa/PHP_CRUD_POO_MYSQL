<?php
    namespace app\models;

    class viewsModels {

        #metodos
        protected function obtenerVistaModelo($vista){
            $listaBlanca = ['dashboard'];

            if(in_array($vista,$listaBlanca)){
                if(is_file("./app/views/content/".$vista."-view.php")){
                    $contenido = "./app/views/content/".$vista."-view.php";
                }else{
                    $contenido = "404";
                }
            }elseif($vista == 'login' || $vista == 'index'){
                $contenido = "login";
            }else{
                $contenido = "404";
            }

            return $contenido;
        }
    }