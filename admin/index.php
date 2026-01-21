<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Login</title>
    <link rel="icon" href="../sj_components/images/general/sj_icon.png" type="image/x-icon">

    <!-- jQuery 1st -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>

    <div class="container login-wrapper d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-6 col-lg-4">

            <div class="login-card bg-white p-5 d-flex align-items-center justify-content-center">
                <!-- CENTERED CONTENT -->
                <div class="w-100" style="max-width: 380px;">

                    <!-- LOGO -->
                    <div class="text-center mb-4">
                        <img class="img-fluid mb-3" src="../sj_components/images/general/gen_off_seajetlogo.svg"
                            alt="Logo" style="max-height: 70px;">
                    </div>

                    <!-- TITLE -->
                    <div class="text-center mb-5">
                        <h3 class="fw-bold">Hello Admin!</h3>
                        <p class="text-muted small">
                            Please login to your account.
                        </p>
                    </div>

                    <!-- FORM -->
                    <form id="frmLogin">

                        <div class="mb-3">
                            <input type="email" name="txtUname" id="txtUname" class="form-control"
                                placeholder="Email address">
                        </div>

                        <div class="mb-3">
                            <input type="password" name="txtPass" id="txtPass" class="form-control"
                                placeholder="Password">
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label small" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <!-- <a href="#" class="small text-decoration-none">Recovery Password</a> -->
                        </div>

                        <input type="button" id="btnLogin" value="Login" class="btn btn-primary w-100 btn-login mt-3">

                    </form>

                </div>
            </div>

        </div>
    </div>

    <script src="js/scripts.js"></script>

</body>

</html>