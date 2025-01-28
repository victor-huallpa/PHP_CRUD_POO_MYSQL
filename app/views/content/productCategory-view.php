<div class="container is-fluid mb-6">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Lista de productos por categoría</h2>
</div>

<div class="container pb-6 pt-6">
    <div class="columns">



        <div class="column is-one-third">
            <h2 class="title has-text-centered">Categorías</h2>
            <?php
                use app\controllers\productController;
                $inscategoria = new productController();
                if(!empty($inscategoria->listarCategoriatoControlador('links'))){
                    echo $inscategoria->listarCategoriatoControlador('links');
                }else{
                    echo '<p class="has-text-centered" >No hay categorías registradas</p>';
                }
            ?>
        </div>



        <div class="column">
            <?php 
                if($url[1]!=""){
                    echo $inscategoria->listarCategoriatoControlador('links',$url[1]);

                    echo $inscategoria->listarProductoControlador($url[2],1,$url[0],"", $url[1]);
                }else{
                    echo '<h2 class="has-text-centered title" >Seleccione una categoría para empezar</h2>';
                }
            ?>
        </div>

    </div>
</div>