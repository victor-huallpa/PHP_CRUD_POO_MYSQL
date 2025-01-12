<body>

    <?php
    use app\controllers\viewsControllers;

    $viewsControllers = new viewsControllers();
    #enviamos la vista que se mostrara
    $vista = $viewsControllers->obtenerVistacontrolador($url[0]);

    #validar vista 
    if($vista == 'login' || $vista == '404'){
        ?>
        <main>
            <?php require_once "./app/views/content/".$vista."-view.php"; ?>
        </main>

        <?php 
        include_once 'layouts/footer.php';
 

    }else{
        include_once 'layouts/header.php';
    ?>
        <main> 
            <?php require_once $vista;?>
        </main>
    <?php
        include_once 'layouts/footer.php';
        include_once 'layouts/script.php';
    }
        
    ?>


</nav>
</body>
</html>