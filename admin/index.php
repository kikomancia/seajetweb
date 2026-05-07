<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — SeaJet Admin</title>
    <link rel="icon" href="../sj_components/images/general/sj_icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-box">
        <div class="login-card">

            <div class="login-logo">
                <img src="../sj_components/images/general/gen_off_seajetlogo.svg" alt="SeaJet">
            </div>

            <div class="login-heading">
                <h2>Admin Portal</h2>
                <p>Sign in to your account to continue.</p>
            </div>

            <form id="frmLogin" autocomplete="off">

                <div class="mb-3">
                    <label for="txtUname">Email address</label>
                    <input type="email" id="txtUname" name="txtUname" class="form-control"
                           placeholder="you@seajet.com" required autofocus>
                </div>

                <div class="mb-4">
                    <label for="txtPass">Password</label>
                    <input type="password" id="txtPass" name="txtPass" class="form-control"
                           placeholder="••••••••" required>
                </div>

                <button type="button" id="btnLogin" class="btn-login">Sign In</button>

            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/login.js"></script>
</body>
</html>
