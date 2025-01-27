<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle">Actualizar imagen de producto</h2>
</div>

<div class="container pb-6 pt-6">
    
    <?php
    $id = $insLogin->limpiarCadena($url[1]);

    include_once './app/views/content/btn_back.php';

    $datos = $insLogin->seleccionarDatos("Unico","producto","producto_id", $id);

    if($datos->rowCount() == 1 ){
        $datos = $datos->fetch();
    ?>

	<div class="form-rest mb-6 mt-6"></div>

	<div class="columns">
		<div class="column is-two-fifths">

        <?php if(is_file("./app/views/resources/photos/productos/".$datos['producto_foto'])){ ?>
			<figure class="image mb-6">
                <img src="<?php echo APP_URL.'app/views/resources/photos/productos/'.$datos['producto_foto']; ?>">
			</figure>
			<form class="FormularioAjax" action="<?php echo APP_URL ?>app/ajax/productosAjax.php" method="POST" autocomplete="off" >

                <input type="hidden" name="modulo_producto" value="eliminarImagenProducto">
				<input type="hidden" name="producto_id" value="<?php echo $datos['producto_id']; ?>">

				<p class="has-text-centered">
					<button type="submit" class="button is-danger is-rounded">Eliminar imagen</button>
				</p>
			</form>

		<?php }else{ ?>

            <figure class="image mb-6">
			  	<img src="<?php echo APP_URL; ?>app/views/resources/photos/productos/producto.png">
			</figure>
        <?php } ?>

		</div>


		<div class="column">
			<form class="mb-6 has-text-centered FormularioAjax" action="<?php echo APP_URL ?>app/ajax/productosAjax.php" method="POST" enctype="multipart/form-data" autocomplete="off" >

                <input type="hidden" name="modulo_producto" value="actualizarImagenProducto">
                <input type="hidden" name="producto_id" value="<?php echo $datos['producto_id']; ?>">

				<h4 class="title is-4 mb-6"><?php echo $datos['producto_nombre']; ?></h4>
				
				<label>Foto o imagen del producto</label><br>

				<div class="file has-name is-horizontal is-justify-content-center mb-6">
				  	<label class="file-label">
				    	<input class="file-input" type="file" name="producto_foto" accept=".jpg, .png, .jpeg" >
				    	<span class="file-cta">
				      		<span class="file-label">Imagen</span>
				    	</span>
				    	<span class="file-name">JPG, JPEG, PNG. (MAX 3MB)</span>
				  	</label>
				</div>
				<p class="has-text-centered">
					<button type="submit" class="button is-success is-rounded">Actualizar imagen</button>
				</p>
			</form>
		</div>
	</div>
    <?php 
        }else{
            include_once './app/views/content/error_alert.php';
        }
    ?>

</div>