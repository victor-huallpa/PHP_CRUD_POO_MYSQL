<div class="container is-fluid mb-6">
    <h1 class="title">Categorías</h1>
    <h2 class="subtitle">Lista de categoría</h2>
</div>

<div class="container pb-6 pt-6">

<?php
    use app\controllers\categoryController;

    $insControlador = new categoryController();

    echo $insControlador->listarCategoriaControlador($url[1],2,$url[0],"");
?>

</div>