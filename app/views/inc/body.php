<body>

    <?php
        use app\controllers\viewsController;
        use app\controllers\loginController;

        $insLogin = new loginController();

        $viewsController= new viewsController();
        $vista=$viewsController->obtenerVistasControlador($url[0]);

    #validar vista 
    if($vista == 'login' || $vista == '404'){
        ?>
        <main>
            <?php require_once "./app/views/content/".$vista."-view.php"; ?>
        </main>

        <?php 
        include_once 'layouts/footer.php';
 

    }else{
        # Cerrar sesion #
        if((!isset($_SESSION['id']) || $_SESSION['id']=="") || (!isset($_SESSION['usuario']) || $_SESSION['usuario']=="")){
            $insLogin->cerrarSesionControlador();
            exit();
        }

        include_once 'layouts/header.php';
    ?>
        <main> 
            <?php require_once $vista;?>
        </main>
    <?php
        include_once 'layouts/footer.php';
    }
    include_once 'layouts/script.php';
        
    ?>


</nav>
</body>
</html>