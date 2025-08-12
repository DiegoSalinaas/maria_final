
<!DOCTYPE html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>CompuClick</title>
        <!-- base:css -->
        <link rel="stylesheet" href="vendors/typicons/typicons.css">
        <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
        <!-- endinject -->
        <!-- plugin css for this page -->
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <link rel="stylesheet" href="css/vertical-layout-light/style.css">
        <link rel="stylesheet" href="css/sweetalert2/sweetalert2.min.css">
        <!-- endinject -->
        <link rel="shortcut icon" href="images/favicon.png" />
    </head>

    <body>
        <div class="container-scroller">
            <div class="container-fluid page-body-wrapper full-page-wrapper">
                <div class="content-wrapper d-flex align-items-center auth px-0">
                    <div class="row w-100 mx-0">
                        <div class="col-lg-4 mx-auto">
                            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                                <div class="brand-logo">
                                    <img src="images/logo-dark.svg" style="width: 350px;"  alt="logo">
                                </div>
                                <h4>¡Hola! ¡Comencemos!</h4>
                                <?php
                                if (isset($error_sesion)) {
                                    ?>
                                    <script>
                                        Swal.fire({
                                            title: "ATENCION",
                                            text: "<?=$error_sesion?>",
                                            icon: "info"
                                        });
                                    </script>
                                    <?php
                                }
                                ?>
                                <h6 class="font-weight-light">Inicia sesión para continuar.</h6>
                                <form class="pt-3" method="POST">
                                    <div class="form-group">
                                        <input type="text" name="usuario" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Usuario">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="pass" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Contraseña">
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium " >INICIAR SESIÓN</button>
                                    </div>
                                    <div class="my-2 d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <label class="form-check-label text-muted">
                                                <input type="checkbox" class="form-check-input">
                                                Mantenerme conectado.
                                            </label>
                                        </div>
                                        <a href="#" class="auth-link text-black">¿Olvidaste tu contraseña?</a>
                                    </div>


                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- page-body-wrapper ends -->
        </div>
        <!-- container-scroller -->
        <!-- base:js -->
        <script src="vendors/js/vendor.bundle.base.js"></script>
        <!-- endinject -->
        <!-- inject:js -->
        <script src="js/off-canvas.js"></script>
        <script src="js/hoverable-collapse.js"></script>
        <script src="js/template.js"></script>
        <script src="js/settings.js"></script>
        <script src="js/todolist.js"></script>
        <script src="js/sweet/sweetalert2.all.min.js"></script>
        <!-- endinject -->
    </body>

</html>
