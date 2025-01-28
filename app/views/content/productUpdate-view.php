<div class="container is-fluid mb-6">
	<h1 class="title">Productos</h1>
	<h2 class="subtitle">Actualizar producto</h2>
</div>
<div class="container pb-6 pt-6">
    <?php
        use app\controllers\productController;

        $id = $insLogin->limpiarCadena($url[1]);
        include_once './app/views/content/btn_back.php';

        $datos = $insLogin->seleccionarDatos("Unico","producto","producto_id", $id);
    
        if($datos->rowCount() == 1 ){
            $datos = $datos->fetch();
    ?>

	<div class="form-rest mb-6 mt-6"></div>
	
	<h2 class="title has-text-centered"><?php echo $datos['producto_nombre']; ?></h2>

	<form action="<?php echo APP_URL; ?>app/ajax/productosAjax.php" method="POST" class="FormularioAjax" autocomplete="off" >

		<input type="hidden" name="modulo_producto" value="actualizar">
		<input type="hidden" name="producto_id" required value="<?php echo $datos['producto_id']; ?>" >

		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Código de barra</label>
				  	<input class="input" type="text" name="producto_codigo" pattern="[a-zA-Z0-9- ]{1,70}" maxlength="70" required value="<?php echo $datos['producto_codigo']; ?>">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Nombre</label>
				  	<input class="input" type="text" name="producto_nombre" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}" maxlength="70" required value="<?php echo $datos['producto_nombre']; ?>">
				</div>
		  	</div>
		</div>
		<div class="columns">
		  	<div class="column">
		    	<div class="control">
					<label>Precio</label>
				  	<input class="input" type="text" name="producto_precio" pattern="[0-9.]{1,25}" maxlength="25" required value="<?php echo $datos['producto_precio']; ?>">
				</div>
		  	</div>
		  	<div class="column">
		    	<div class="control">
					<label>Stock</label>
				  	<input class="input" type="text" name="producto_stock" pattern="[0-9]{1,25}" maxlength="25" required value="<?php echo $datos['producto_stock']; ?>">
				</div>
		  	</div>
		  	<div class="column">
				<label>Categoría</label><br>
		    	<div class="select is-rounded">
				  	<select name="producto_categoria" >
                      <?php
                            $inscategoria = new productController();
                            echo $inscategoria->listarCategoriatoControlador('opciones');
				    	?>
				  	</select>
				</div>
		  	</div>
		</div>
		<p class="has-text-centered">
			<button type="submit" class="button is-success is-rounded">Actualizar</button>
		</p>
	</form>

    <?php 
        }else{
            include "./inc/error_alert.php";
        }
        $check_categoria=null;
    ?>


</div>