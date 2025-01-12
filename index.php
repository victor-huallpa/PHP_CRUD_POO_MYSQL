<?php
    require_once './config/app.php';
    require_once './autoload.php';
    include_once './app/views/inc/session_start.php';

    if(isset($_GET['views'])){
        $url = explode("/",$_GET['views']);
    }else{
        $url = ['login'];
    }

    include_once './app/views/inc/head.php';
    include_once './app/views/inc/body.php';

?>

