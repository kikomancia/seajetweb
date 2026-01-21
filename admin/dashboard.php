<?php

    include('php_files/fetch_data.php');

    // session_start();

    // if (!isset($_SESSION['logged'])) {
    //     header("Location: index.php");
    //     exit;
    // }

    // // Get user name
    // $disp_name = $_SESSION['disp_name']

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="icon" href="../sj_components/images/general/sj_icon.png" type="image/x-icon">
    <title>SJ Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="#"><img src="../sj_components/images/general/gen_off_seajetlogo.svg" alt="" srcset="" height="45px"> </a>

        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>

        <div class="d-none d-md-inline-block ms-auto me-0 me-md-3 my-2 my-md-0">
            <span class="fw-semibold" style="color: white;">
                Hi, <?php echo $_SESSION['disp_name']; ?>
            </span>
        </div>

        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <!-- <li><a class="dropdown-item" href="#!">Settings</a></li>
                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li> -->
                    <li><a class="dropdown-item" href="php_files/logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>


    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">

                        <div class="sb-sidenav-menu-heading">Admin Panel</div>
                        <a class="nav-link" href="#">
                            <div class="sb-nav-link-icon"> <i class="fa-solid fa-comment-dots"></i> </div>
                            Client Inquiries
                        </a>

                        <div class="sb-sidenav-menu-heading"> </div>

                    </div>


                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <div>
                        <?php echo $_SESSION['disp_name']; ?></div>

                </div>
            </nav>
        </div>


        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h2 class="mt-4">Client Inquiries</h2>
                    <!--                     
                    <div class="row">
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">New message</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="#">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Read Messages</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="#">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Replied Messages</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a class="small text-white stretched-link" href="#">View Details</a>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>

                    </div> -->

                    <div class="card mb-4 mt-4">

                        <!-- <div class="card-header">
                            <label style="font-size: 25px;"><i class='fas fa-user'></i> List of 
                                Inquiries</label>
                        </div> -->

                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Date message</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Message</th>

                                        <!-- <th>Status</th> -->
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($fetchMessage as $fetchMessages) : ?>

                                        <tr>
                                            <td>
                                                <?php echo date("M d, Y h:i A", strtotime($fetchMessages['date_time'])); ?>

                                            </td>
                                            <td style="text-align: left;">
                                                <?php echo $fetchMessages['cli_name']; ?>
                                            </td>
                                            <td style="text-align: left; width: auto !important;">
                                                <?php echo $fetchMessages['cli_email']; ?>
                                            </td>
                                            <td>
                                                <?php echo $fetchMessages['cli_num']; ?>
                                            </td>
                                            <td style="text-align:left; width:200px; word-wrap:break-word; white-space:normal;">
                                                <?php echo htmlspecialchars($fetchMessages['cli_message']); ?>
                                            </td>



                                        </tr>

                                    <?php endforeach; ?>
                                </tbody>


                            </table>

                        </div>
                    </div>
                </div>

            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted"> &copy; 2025 Sea-Jet International Forwarders, Inc.</div>

                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>