<?php
    namespace app\models;
    use \PDO;

    if(file_exists(__DIR__."/../../config/server.php")){
        require_once __DIR__."/../../config/server.php";
    }
    class mainModel {
        private $server = DB_SERVER;
        private $db = DB_NAME;
        private $db_user = DB_USER;
        private $db_pass = DB_PASS;

        #metodo de coneccion a la base de datos
        protected function conectar(){
            $conexion = new PDO("mysql:host".$this->server.";dbname".$this->db,$this->db_user,$this->db_pass);
            $conexion->exec("SET CHARACTER SET utf8");
            return $conexion;
        }

        #metodo para ejecutar coneccion a la base de datos
        protected function ejecutarConsulta($consulta){
            $sql = $this->conectar()->prepare($consulta);
            $sql->execute();
            return $sql;
        }

        #metoddo de filtrado de inyeccion sql y codigo
        public function limpiarCadena($cadena){
            $palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

            $cadena = trim($cadena);//limpia los espacios
            $cadena = stripslashes($cadena);//quila una barra envertida

            foreach($palabras as $palabra){
                $cadena = str_ireplace($palabra, "", $cadena);
            }

            $cadena = trim($cadena);//limpia los espacios
            $cadena = stripslashes($cadena);//quila una barra envertida

            return $cadena;
        }

        #metodo de filtrado y validacion de datos de acuerdo a los pathernd de html
        protected function verificarDatos($filtro, $cadena){
            if(preg_match("/^".$filtro."$/",$cadena)){
                return false;
            }else{
                return true;
            }
        }

        #metodo para guardar datos en la base de datos
        protected function guardarDatos($tabla, $datos){
            $query = "INSERT INTO $tabla (";

            #iterar los campos de la tabla a llenar
            $c = 0;
            foreach($datos as $clave){
                if($c >= 1){$query.=",";}
                $query.=$clave['campo_nombre'];
                $c++;
            }

            $query .= ") VALUES(";

            #iterar los datos que se llenaran en la tabla
            $c = 0;
            foreach($datos as $clave){
                if($c >= 1){$query.=",";}
                $query.=$clave['campo_marcador'];
                $c++;
            }

            $query .= ")";

            $sql = $this->conectar()->prepare($query);

            foreach($datos as $clave){
                $sql->bindParam($clave['campo_marcador'],$clave['campo_valor']);
            }

            $sql->execute();

            return $sql;
            
        }

        #metodo para seleccionar datos de la base de datos
        public function seleccionarDatos($tipo, $tabla, $campo, $id){
            $tipo = $this->limpiarCadena($tipo);
            $tabla = $this->limpiarCadena($tabla);
            $campo = $this->limpiarCadena($campo);
            $id = $this->limpiarCadena($id);

            if($tipo == 'Unico'){
                $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo = :ID");
                $sql->bindParam(":ID",$id['campo_nombre']);

            }elseif($tipo == 'Normal'){
                $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla");
            }

            $sql->execute();

            return $sql;
        }

        #metodo para actualizar datos de la base de datos
        protected function actualizarDatos($tabla, $datos, $condicion){
            $query = "UPDATE $tabla SET ";
            #iterar los datos que se ACTAULIZARAN en la tabla
            $c = 0;
            foreach($datos as $clave){
                if($c >= 1){$query.=",";}
                $query.=$clave['campo_marcador']."=".$clave['campo_marcador'];
                $c++;
            }

            $query .= " WHERE ".$condicion["condicion_campo"]."=".$condicion["condicion_marcador"];

            $sql = $this->conectar()->prepare($query);

            foreach($datos as $clave){
                $sql->bindParam($clave['campo_marcador'],$clave['campo_valoe']);
            }

            $sql->bindParam($condicion['codicion_marcador'],$clave['condicion_valoe']);

            $sql->execute();

            return $sql;

        }

        #metodo para eliminar datos de la base de datos
        protected function eliminarRegistro($tabla, $campo, $id){
            $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo = :id");
            $sql->bindParam(":id",$id);
            $sql->execute();

            return $sql;

        }

        #metodo boton de paginacion de tablas o listas
        protected function paginadorTablas($pagina,$numeroPaginas,$url,$botones){

            $tabla = '<nav class="pagination is-centered is-rounded" role="navigation" aria-label="pagination">';

            if($pagina <= 1){
                $tabla .= '
                    <a class="pagination-previous is-disabled" disabled >Anterior</a>
                    <ul class="pagination-list">
                ';
            }else{
                $tabla .= '
                <a class="pagination-previous" href="'.$url.($pagina-1).'/">Anterior</a>
                <ul class="pagination-list">
                    <li><a class="pagination-link" href="'.$url.'1/">1</a></li>
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                ';
            }

            $ci = 0;
            for($i = $pagina; $i <= $numeroPaginas; $i++){
                if($ci >= $botones){
                    break;
                }

                if($pagina == $i){
                    $tabla .= '<li><a class="pagination-link is-current" href="'.$url.$i.'/">'.$pagina.'</a></li>';
                }else{
                    $tabla .= '<li><a class="pagination-link" href="'.$url.$i.'/">'.$pagina.'</a></li>';
                }

                $ci++;
            }

            if($pagina == $numeroPaginas){
                $tabla .= '
                    </ul>
                    <a class="pagination-next is-disabled" disabled >Siguiente</a>
                ';
            }else{
                $tabla .= '
                    <li><span class="pagination-ellipsis">&hellip;</span></li>
                    <li><a class="pagination-link" href="'.$url.$numeroPaginas.'/">'.$numeroPaginas.'</a></li>
                </ul>
                <a class="pagination-next" href="'.$url.($pagina+1).'/">Siguiente</a>
                ';
            }

            $tabla .= '</nav>';

            return $tabla;

        }

    }

