<?php
        use app\controllers\viewsController;
        use app\controllers\loginController;

        $insLogin = new loginController();

        $viewsController= new viewsController();
        $vista=$viewsController->obtenerVistasControlador($url[0]);

        $nombreVista = $viewsController->obtenerNombrePaginaControlador($url[0]);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- logo de la pagina -->
    <link rel="shortcut icon" href="<?php echo APP_URL; ?>app/views/resources/images/logo.png" type="image/x-icon">
    <!-- nombre de la pagiana -->
    <title>VECH | <?php echo $nombreVista; ?></title>
    <!-- links de estilos de la pagiana -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/resources/css/style.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/resources/css/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo APP_URL; ?>app/views/resources/css/bulma/css/bulma.min.css">
    <!-- script de estilos -->
     <script src="<?php echo APP_URL; ?>app/views/resources/js/sweetalert2.all.min.js"></script>
</head>
