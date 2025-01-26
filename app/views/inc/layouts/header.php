<header>
    <nav class="navbar">
        <div class="navbar-brand">
            <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard">
                <img src="<?php echo APP_URL; ?>app/views/resources/images/logo.png" alt="Logo de la pagina" width="28" height="28">
            </a>
            <div class="navbar-burger" data-target="navbarExampleTransparentExample">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div id="navbarExampleTransparentExample" class="navbar-menu">

            <div class="navbar-start">
                <a class="navbar-item" href="<?php echo APP_URL; ?>dashboard">
                    Dashboard
                </a>

                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link" href="#">
                        Usuarios
                    </a>
                    <div class="navbar-dropdown is-boxed">

                        <a class="navbar-item" href="<?php echo APP_URL; ?>userNew/">
                            Nuevo
                        </a>
                        <a class="navbar-item" href="<?php echo APP_URL; ?>userList/">
                            Lista
                        </a>
                        <a class="navbar-item" href="<?php echo APP_URL; ?>userSearch/">
                            Buscar
                        </a>

                    </div>
                </div>
            </div>

            <div class="navbar-end">
                <div class="navbar-item has-dropdown is-hoverable">
                    <a class="navbar-link">
                        <?php echo $_SESSION['nombre']; ?>
                    </a>
                    <div class="navbar-dropdown is-boxed">

                        <a class="navbar-item" href="<?php echo APP_URL."userUpdate/".$_SESSION['id']."/"; ?>">
                            Mi cuenta
                        </a>
                        <a class="navbar-item" href="<?php echo APP_URL."userFoto/".$_SESSION['id']."/"; ?>">
                            Mi foto
                        </a>
                        <hr class="navbar-divider">
                        <a class="navbar-item" href="<?php echo APP_URL; ?>logOut/" id="btn_exit" >
                            Salir
                        </a>

                    </div>
                </div>
            </div>

        </div>
    </nav>
</header>
