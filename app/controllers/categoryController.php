<?php

	namespace app\controllers;
	use app\models\mainModel;
    

	class categoryController extends mainModel{

        /*----------  Controlador listar categoria  ----------*/
        public function listarCategoriaControlador($pagina,$registros,$url,$busqueda){
            $pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

            $busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            //consultas nysql para listar las categorias
            if(isset($busqueda) && $busqueda!=""){

                $consulta_datos="SELECT * FROM Categoria WHERE categoria_id LIKE '%$busqueda%' OR categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%' ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
        
                $consulta_total="SELECT COUNT(categoria_id) FROM Categoria WHERE categoria_id LIKE '%$busqueda%' OR categoria_nombre LIKE '%$busqueda%' OR categoria_ubicacion LIKE '%$busqueda%'";
        
        
            }else{
        
                $consulta_datos="SELECT * FROM Categoria ORDER BY categoria_nombre ASC LIMIT $inicio,$registros";
        
                $consulta_total="SELECT COUNT(categoria_id) FROM Categoria ";
                
            }

            $datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();

			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();

			$numeroPaginas =ceil($total/$registros);

            $tabla.='
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
                    <thead>
                        <tr class="has-text-centered">
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Productos</th>
                            <th colspan="2">Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
            ';
            if($total>=1 && $pagina<=$numeroPaginas){
                $contador=$inicio+1;
                $pag_inicio=$inicio+1;
                foreach($datos as $rows){
                    $tabla.='
                        <tr class="has-text-centered" >
                            <td>'.$contador.'</td>
                            <td>'.$rows['categoria_nombre'].'</td>
                            <td>'.substr($rows['categoria_ubicacion'],0,25).'</td>
                            <td>
                                <a href="'.APP_URL.'productCategory/'.$rows['categoria_id'].'/" class="button is-link is-rounded is-small">Ver productos</a>
                            </td>
                            <td>
                                <a href="'.APP_URL.'categoryUpdate/'.$rows['categoria_id'].'/" class="button is-success is-rounded is-small">Actualizar</a>
                            </td>
                            <td>
                                <form class="FormularioAjax" action="'.APP_URL.'app/ajax/categoriasAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_categoria" value="eliminar">
			                		<input type="hidden" name="categoria_id" value="'.$rows['categoria_id'].'">

			                    	<button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
			                    </form>
            
                            </td>
                        </tr>
                    ';
                    $contador++;
                }
                $pag_final=$contador-1;
            }else{
                if($total>=1){
                    $tabla.='
                        <tr class="has-text-centered" >
                            <td colspan="6">
                                <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                                    Haga clic acá para recargar el listado
                                </a>
                            </td>
                        </tr>
                    ';
                }else{
                    $tabla.='
                        <tr class="has-text-centered" >
                            <td colspan="7">
                                No hay registros en el sistema
                            </td>
                        </tr>
                    ';
                }
            } 
            
            $tabla.='</tbody></table></div>';

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;

			return $tabla;

        }

		/*----------  Controlador registrar categoria  ----------*/
        public function registrarCategoriaControlador(){
            // echo 'entraste';
            # Almacenando datos#
		    $categoria=$this->limpiarCadena($_POST['categoria_nombre']);
		    $ubicacion=$this->limpiarCadena($_POST['categoria_ubicacion']);

            # Verificando campos obligatorios #
		    if($categoria==""){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$categoria)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoria ingresada no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();

		    }

            if($ubicacion != ""){
                if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)) {
                    
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"La UBICACION no cumple con el formato solicitado",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                } 
            }

            # Verificando usuario #
		    $check_categoria=$this->ejecutarConsulta("SELECT categoria_nombre FROM Categoria WHERE categoria_nombre='$categoria'");
		    if($check_categoria->rowCount()>0){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoria ingresada ya se encuentra registrado, por favor elija otra categoria",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }
          
            $categoria_datos_reg=[
				[
					"campo_nombre"=>"categoria_nombre",
					"campo_marcador"=>":Categoria",
					"campo_valor"=>$categoria
				],
				[
					"campo_nombre"=>"categoria_ubicacion",
					"campo_marcador"=>":Ubicacion",
					"campo_valor"=>$ubicacion
				]
			];
            
			$registrar_categoria=$this->guardarDatos("Categoria",$categoria_datos_reg);

            if($registrar_categoria->rowCount()==1){
				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Categoria registrada",
					"texto"=>"La categoria ".$categoria." se registro con exito",
					"icono"=>"success"
				];
			}else{

				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar la categoria, por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);


        }

        /*----------  Controlador eliminar categoria  ----------*/
        public function eliminarCategoriaControlador(){
			$id=$this->limpiarCadena($_POST['categoria_id']);

            # Verificando categoria #
		    $datos=$this->ejecutarConsulta("SELECT * FROM Categoria WHERE categoria_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado la categoria en el sistema, intente nuevamente",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            # Verificando productos asociados a la categoria #
		    $producto=$this->ejecutarConsulta("SELECT categoria_id FROM producto WHERE categoria_id = '".$datos['categoria_id']."' LIMIT 1");
		    if($producto->rowCount()==1){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoria no se puede eliminar por que contiene productos asociados",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

            # Eliminamos la categorias antes que se reprodusca #
		    $eliminarCategoria=$this->eliminarRegistro("Categoria","categoria_id",$id);

            if($eliminarCategoria->rowCount()==1){

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario eliminado",
					"texto"=>"La categoria ".$datos['categoria_nombre']." ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar la categoria ".$datos['categoria_nombre']." del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);


        }

        /*----------  Controlador actualizar categoria  ----------*/
        public function actualizarCategoriaControlador(){
			$id=$this->limpiarCadena($_POST['categoria_id']);

            # Verificando categoria #
		    $datos=$this->ejecutarConsulta("SELECT * FROM Categoria WHERE categoria_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La CATEGORIA no existe en el sistema.",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            /*== Almacenando datos dela categoria ==*/
            $nombre=$this->limpiarCadena($_POST['categoria_nombre']);
            $ubicacion=$this->limpiarCadena($_POST['categoria_ubicacion']);

            # Verificando campos obligatorios admin #
		    if($nombre=="" ){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No ha llenado los campos que son obligatorios, intentalo de nuevo",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

            # Verificando integridad de los datos
            if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>" El nombre de la categoria no cumple con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

            if($ubicacion != ""){
                if ($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)) {
                    $alerta=[
                        "tipo"=>"simple",
                        "titulo"=>"Ocurrió un error inesperado",
                        "texto"=>"  La UBICACION no cumple con el formato solicitado",
                        "icono"=>"error"
                    ];
                    return json_encode($alerta);
                    exit();
                } 
            }

            # Verificando nombre de la categoria
            if($nombre != $datos['categoria_nombre']){
                $check_nombre = $this->ejecutarConsulta("SELECT categoria_nombre FROM Categoria WHERE categoria_nombre = '$nombre'");

                if ($check_nombre->rowCount() > 0) {
                    $alerta=[
                            "tipo"=>"simple",
                            "titulo"=>"Ocurrió un error inesperado",
                            "texto"=>" El NOMBRE DE LA CATEGORIA ya está registrado, por favor seleecione otro nombre.",
                            "icono"=>"error"
                        ];
                    return json_encode($alerta);
                    exit();
                }
            }

            $categoria_datos_up=[
				[
					"campo_nombre"=>"categoria_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"categoria_ubicacion",
					"campo_marcador"=>":Ubicacion",
					"campo_valor"=>$ubicacion
				]
			];

			$condicion=[
				"condicion_campo"=>"categoria_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("Categoria",$categoria_datos_up,$condicion)){

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario actualizado",
					"texto"=>"Los datos de la categoria ".$datos['categoria_nombre']." se actualizaron a ".$nombre." correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido actualizar los datos del usuario ".$datos['categoria_nombre']." por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
        }

    }

    
    
