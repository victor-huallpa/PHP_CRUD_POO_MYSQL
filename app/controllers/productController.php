<?php

	namespace app\controllers;
	use app\models\mainModel;
    

	class productController extends mainModel{

        /*----------  Controlador listar producto  ----------*/
        public function listarProductoControlador($pagina,$registros,$url,$busqueda){
            $pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

            $busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            // ojo
            $categoria_id = (isset($_GET['category_id']))?$_GET['category_id']:0;


        	$campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto.producto_precio,producto.producto_stock,producto.producto_foto,Categoria.categoria_nombre,usuarios.usuario_nombre,usuarios.usuario_apellido";


            //consultas mysql para listar las categorias
            if(isset($busqueda) && $busqueda!=""){

                $consulta_datos="SELECT $campos FROM producto INNER JOIN Categoria ON producto.categoria_id=Categoria.categoria_id INNER JOIN usuarios ON producto.usuario_id=usuarios.usuario_id WHERE producto.producto_codigo LIKE '%$busqueda%' OR producto.producto_nombre LIKE '%$busqueda%' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
        
                $consulta_total="SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%'";
        
            }elseif($categoria_id > 0){
        
                $consulta_datos="SELECT $campos FROM producto INNER JOIN Categoria ON producto.categoria_id=Categoria.categoria_id INNER JOIN usuarios ON producto.usuario_id=usuarios.usuario_id WHERE producto.categoria_id = $categoria_id ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
        
                $consulta_total="SELECT COUNT(producto_id) FROM producto WHERE categoria_id = $categoria_id ";
        
            }else{
        
                $consulta_datos="SELECT $campos FROM producto INNER JOIN Categoria ON producto.categoria_id=Categoria.categoria_id INNER JOIN usuarios ON producto.usuario_id=usuarios.usuario_id ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";
        
                $consulta_total="SELECT COUNT(producto_id) FROM producto ";
                
            }
        
            $datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();

			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();

			$numeroPaginas =ceil($total/$registros);

            if($total>=1 && $pagina<=$numeroPaginas){
                $contador=$inicio+1;
                $pag_inicio=$inicio+1;
                foreach($datos as $rows){
                    $tabla.='
                        <article class="media">
                            <figure class="media-left">
                                <p class="image is-64x64">';
                                if(is_file("./app/views/resources/photos/productos/".$rows['producto_foto'])){
                                    $tabla.='<img src="'.APP_URL.'app/views/resources/photos/productos/'.$rows['producto_foto'].'">';
                                }else{
                                    $tabla.='<img src="'.APP_URL.'app/views/resources/photos/productos/producto.png"> hola';
        
                                }
                    $tabla.='   </p>
                            </figure>
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong>'.$contador.' - '.$rows['producto_nombre'].'</strong><br>
                                        <strong>CODIGO:</strong> '.$rows['producto_codigo'].',   
                                        <strong>  PRECIO:</strong> $'.$rows['producto_precio'].',   
                                        <strong>  STOCK:</strong> '.$rows['producto_stock'].',  
                                        <strong>  CATEGORIA:</strong> '.$rows['categoria_nombre'].',   
                                        <strong>  REGISTRADO POR:</strong> '.$rows['usuario_nombre'].' '.$rows['usuario_apellido'].'
                                    </p>
                                </div>
                                <div class="has-text-right">
                                   
                                    <a href="'.APP_URL.'productFoto/'.$rows['producto_id'].'/" class="button is-link is-rounded is-small">Imagen</a>
                                  
                                    <a href="'.APP_URL.'productUpdate/'.$rows['producto_id'].'/" class="button is-success is-rounded is-small">Actualizar</a>

                                    <form class="FormularioAjax" action="'.APP_URL.'app/ajax/productosAjax.php" method="POST" autocomplete="off" style="display: inline-block; margin-left: 5px;">
                                        <input type="hidden" name="modulo_usuario" value="eliminar">
                                        <input type="hidden" name="usuario_id" value="'.$rows['producto_id'].'">
                                        <input type="submit" value="Eliminar" class="button is-danger is-rounded is-small">
                                    </form>
                                </div>
                            </div>
                        </article>
        
        
                        <hr>
                    ';
                    $contador++;
                }
                $pag_final=$contador-1;
            }else{
                if($total>=1){
                    $tabla.='
                    <p class="has-text-centered">
                        <a href="'.$url.'1" class="button is-link is-rounded is-small mt-4 mb-4">
                            Haga clic acá para recargar el listado
                        </a>
                    </p>
                    ';
                }else{
                    $tabla.='<p class="has-text-centered">No hay registros en el sistema</p>
                    ';
                }
            }

            if($total>=1 && $pagina<=$numeroPaginas){
                $tabla .= '
                        <p class="has-text-right">Mostrando productos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>
                ';
            }

			return $tabla;


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
        }

		/*----------  Controlador registrar producto  ----------*/
        public function registrarProductoControlador(){

            /*== Almacenando datos ==*/
            $codigo=$this->limpiarCadena($_POST['producto_codigo']);
            $nombre=$this->limpiarCadena($_POST['producto_nombre']);

            $precio=$this->limpiarCadena($_POST['producto_precio']);
            $stock=$this->limpiarCadena($_POST['producto_stock']);
            $categoria=$this->limpiarCadena($_POST['producto_categoria']);

            /*== Verificando campos obligatorios ==*/
            if($codigo=="" || $nombre=="" || $precio=="" || $stock=="" || $categoria==""){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            /*== Verificando integridad de los datos ==*/
            if($this->verificarDatos("[a-zA-Z0-9 ]{1,70}",$codigo)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El codigo ingresada no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            if($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El nombre del producto no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            if($this->verificarDatos("[0-9.]{1,25}",$precio)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO del producto no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            if($this->verificarDatos("[0-9]{1,25}",$stock)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El STOCK del producto no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }
            if($this->verificarDatos("[0-9]{1,25}",$categoria)){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoria del producto no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

             /*== Verificando codigo ==*/
            $check_codigo=$this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
            if($check_codigo->rowCount()>0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El CODIGO de BARRAS ingresado ya se encuentra registrado, por favor elija otro",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            /*== Verificando nombre ==*/
            $check_nombre=$this->ejecutarConsulta("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
            if($check_nombre->rowCount()>0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE ingresado ya se encuentra registrado, por favor elija otro",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            /*== Verificando categoria ==*/
            $check_categoria=$this->ejecutarConsulta("SELECT categoria_id FROM Categoria WHERE categoria_id='$categoria'");
            if($check_categoria->rowCount()<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>" La categoría seleccionada no existe",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            /* Directorios de imagenes */
            $img_dir="../views/resources/photos/productos/";

            /*== Comprobando si se ha seleccionado una imagen ==*/
    		if($_FILES['producto_foto']['name']!="" && $_FILES['producto_foto']['size']>0){
 
    			# Creando directorio #
		        if(!file_exists($img_dir)){
		            if(!mkdir($img_dir,0777)){
		            	$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"Error al crear el directorio",
							"icono"=>"error"
						];
						return json_encode($alerta);
		                exit();
		            } 
		        }

		        # Verificando formato de imagenes #
		        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado es de un formato no permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Verificando peso de imagen #
		        if(($_FILES['producto_foto']['size']/1024)>5120){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado supera el peso permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Nombre de la foto #
		        $foto=str_ireplace(" ","_",$nombre);
		        $foto=$foto."_".rand(0,100);

		        # Extension de la imagen #
		        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
		            case 'image/jpeg':
		                $foto=$foto.".jpg";
		            break;
		            case 'image/png':
		                $foto=$foto.".png";
		            break;
		        }

		        chmod($img_dir,0777);

		        # Moviendo imagen al directorio #
		        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"No podemos subir la imagen al sistema en este momento",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

    		}else{
    			$foto="";
    		}

            //Almacenar datos en un array
            $producto_datos_reg=[
				[
					"campo_nombre"=>"producto_codigo",
					"campo_marcador"=>":Codigo",
					"campo_valor"=>$codigo
				],
				[
					"campo_nombre"=>"producto_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"producto_precio",
					"campo_marcador"=>":Precio",
					"campo_valor"=>$precio
				],
				[
					"campo_nombre"=>"producto_stock",
					"campo_marcador"=>":Stock",
					"campo_valor"=>$stock
				],
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"categoria_id",
					"campo_marcador"=>":Id",
					"campo_valor"=>$categoria
				],
				[
					"campo_nombre"=>"usuario_id",
					"campo_marcador"=>":id",
					"campo_valor"=>$_SESSION['id']
				]
			];

			$registrar_producto=$this->guardarDatos("producto",$producto_datos_reg);
            

			if($registrar_producto->rowCount()==1){
				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Usuario registrado",
					"texto"=>"El producto ".$nombre." se registro con exito",
					"icono"=>"success"
				];
			}else{
				
				if(is_file($img_dir.$foto)){
		            chmod($img_dir.$foto,0777);
		            unlink($img_dir.$foto);
		        }

				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar el producto ".$nombre.", por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
        }

        /*----------  Controlador eliminar producto  ----------*/
        public function eliminarProductoControlador(){
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

        /*----------  Controlador actualizar producto  ----------*/
        public function actualizarProductoControlador(){
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

        /*----------  Controlador listar categoria  ----------*/
        public function listarCategoriatoControlador(){
            
            $datos = $this->ejecutarConsulta("SELECT categoria_nombre,categoria_id FROM Categoria");

            $categorias = "";
            if($datos->rowCount()>0){
                $datos=$datos->fetchAll();
                foreach($datos as $row){
                    $categorias .= '<option value="'.$row['categoria_id'].'" >'.$row['categoria_nombre'].'</option>';
                }
            }
            return $categorias;
        }

    }