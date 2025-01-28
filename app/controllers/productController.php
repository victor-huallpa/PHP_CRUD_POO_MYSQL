<?php

	namespace app\controllers;
	use app\models\mainModel;
    

	class productController extends mainModel{

        /*----------  Controlador listar producto  ----------*/
        public function listarProductoControlador($pagina,$registros,$url,$busqueda, $id = ''){
            $pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);

			$url=$this->limpiarCadena($url);
			$url=APP_URL.$url."/";

            $busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

            $pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

            
            $categoria_id = (!empty($id))?$id:0;


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

				// return $pagina;
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
                                    $tabla.='<img src="'.APP_URL.'app/views/resources/photos/productos/producto.png"> ';
        
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
                                        <input type="hidden" name="modulo_producto" value="eliminar">
                                        <input type="hidden" name="producto_id" value="'.$rows['producto_id'].'">
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

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando productos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p><br/>';

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

			$id=$this->limpiarCadena($_POST['producto_id']);

            # Verificando categoria #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema, intente nuevamente",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            # Eliminamos el producto antes que se reprodusca #
		    $eliminarProducto=$this->eliminarRegistro("producto","producto_id",$id);

            if($eliminarProducto->rowCount()==1){

		        $alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario eliminado",
					"texto"=>"El producto ".$datos['producto_nombre']." ha sido eliminado del sistema correctamente",
					"icono"=>"success"
				];

		    }else{

		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido eliminar el producto ".$datos['producto_nombre']." del sistema, por favor intente nuevamente",
					"icono"=>"error"
				];
		    }

		    return json_encode($alerta);


        }

        /*----------  Controlador actualizar producto  ----------*/
        public function actualizarProductoControlador(){
			$id=$this->limpiarCadena($_POST['producto_id']);

            # Verificando categoria #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El producto no existe en el sistema.",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

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
					"texto"=>"No ha llenado los campos que son obligatorios, intentalo de nuevo",
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
            if($codigo != $datos['producto_codigo']){
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
            }
  
              /*== Verificando nombre ==*/
            if($nombre != $datos['producto_nombre']){
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
            }
  
              /*== Verificando categoria ==*/
            if($categoria != $datos['categoria_id']){
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
            }  

            //Almacenar datos en un array
            $producto_datos_up=[
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
					"campo_nombre"=>"categoria_id",
					"campo_marcador"=>":Id",
					"campo_valor"=>$categoria
				]
			];

			$condicion=[
				"condicion_campo"=>"producto_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("producto",$producto_datos_up,$condicion)){

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Usuario actualizado",
					"texto"=>"Los datos del producto ".$datos['producto_nombre']." se actualizaron correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos podido actualizar los datos del producto ".$datos['producto_nombre']." por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
        }

        /*----------  Controlador listar categoria  ----------*/
        public function listarCategoriatoControlador($forma, $id=''){
			$forma=$this->limpiarCadena($forma);
			$id=$this->limpiarCadena($id);
            
			$condicion = !empty($id) ? "WHERE categoria_id='$id'" : "";
            $datos = $this->ejecutarConsulta("SELECT categoria_nombre,categoria_id,categoria_ubicacion FROM Categoria $condicion");

			if(!empty($condicion)){
                $datos=$datos->fetch();
				$texto = '
					<h2 class="title has-text-centered">'.$datos['categoria_nombre'].'</h2>
					<p class="has-text-centered pb-6" >'.$datos['categoria_ubicacion'].'</p>
				';
				return $texto;
				exit();
			}
            $categorias = "";
            if($datos->rowCount()>0){
                $datos=$datos->fetchAll();

                if($forma == 'opciones'){
                    foreach($datos as $row){
                        $categorias .= '<option value="'.$row['categoria_id'].'" >'.$row['categoria_nombre'].'</option>';
                    }
                }elseif($forma == 'links'){
                    foreach($datos as $row){
                        $categorias .= '

						<a href="'.APP_URL.'productCategory/'.$row['categoria_id'].'/" class="button is-link is-inverted is-fullwidth">'.$row['categoria_nombre'].'</a>';
                    }
                }

            }
            return $categorias;
        }

        /*----------  Controlador actualizar imagen producto  ----------*/
        public function actualizarImagenProductoControlador(){
            $id=$this->limpiarCadena($_POST['producto_id']);

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

            # Directorio de imagenes #
			$img_dir="../views/resources/photos/productos/";

    		# Comprobar si se selecciono una imagen #
    		if($_FILES['producto_foto']['name']=="" && $_FILES['producto_foto']['size']<=0){
    			$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No ha seleccionado una foto para el producto",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
    		}

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
	        if($datos['producto_foto']!=""){
		        $foto=explode(".", $datos['producto_foto']);
		        $foto=$foto[0];
	        }else{
	        	$foto=str_ireplace(" ","_",$datos['producto_nombre']);
	        	$foto=$foto."_".mt_rand(0,100);
	        }

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

            # Eliminando imagen anterior #
	        if(is_file($img_dir.$datos['producto_foto']) && $datos['producto_foto']!=$foto){
		        chmod($img_dir.$datos['producto_foto'], 0777);
		        unlink($img_dir.$datos['producto_foto']);
		    }

		    $producto_datos_up=[
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				]
			];

			$condicion=[
				"condicion_campo"=>"producto_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

            if($this->actualizarDatos("producto",$producto_datos_up,$condicion)){

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"La foto del producto ".$datos['producto_nombre']." se actualizo correctamente",
					"icono"=>"success"
				];
			}else{

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto actualizada",
					"texto"=>"No hemos podido actualizar la foto del producto ".$datos['producto_nombre'],
					"icono"=>"warning"
				];
			}

			return json_encode($alerta);

        }

        /*----------  Controlador eliminar imagen producto  ----------*/
        public function eliminarImagenProductoControlador(){
            $id=$this->limpiarCadena($_POST['producto_id']);

			# Verificando usuario #
		    $datos=$this->ejecutarConsulta("SELECT * FROM producto WHERE producto_id='$id'");
		    if($datos->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado el producto en el ",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }else{
		    	$datos=$datos->fetch();
		    }

		    # Directorio de imagenes #
			$img_dir="../views/resources/photos/productos/";

    		chmod($img_dir,0777);

    		if(is_file($img_dir.$datos['producto_foto'])){

		        chmod($img_dir.$datos['producto_foto'],0777);

		        if(!unlink($img_dir.$datos['producto_foto'])){
		            $alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"Error al intentar eliminar la foto del producto, por favor intente nuevamente",
						"icono"=>"error"
					];
					return json_encode($alerta);
		        	exit();
		        }
		    }else{
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No hemos encontrado la foto del producto en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    $producto_datos_up=[
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>""
				]
			];

			$condicion=[
				"condicion_campo"=>"producto_id",
				"condicion_marcador"=>":ID",
				"condicion_valor"=>$id
			];

			if($this->actualizarDatos("producto",$producto_datos_up,$condicion)){

				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto eliminada",
					"texto"=>"La foto del producto ".$datos['producto_nombre']." se elimino correctamente",
					"icono"=>"success"
				];
			}else{
				$alerta=[
					"tipo"=>"recargar",
					"titulo"=>"Foto eliminada",
					"texto"=>"No hemos podido actualizar la foto del producto ".$datos['producto_nombre'],
					"icono"=>"warning"
				];
			}

			return json_encode($alerta);
        }

    }