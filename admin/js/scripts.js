/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});



// login page js
$(document).on("click", "#btnLogin", function () {

    $.ajax({
        url: "../admin/php_files/login.php",
        type: "POST",
        data: $("#frmLogin").serialize(),
        success: function (response) {

            if (response.statusCode === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    timer: 1200,
                    footer: 'SeaJet International Inc.',
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = "../admin/dashboard.php";
                });

            } else if (response.statusCode === 201) {
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color:#FF003C;"> Oops!',
                    text: 'Please fill out all required fields!',
                    footer: 'SeaJet International Inc.',
                });

            } 
            else if (response.statusCode === 202) {
                Swal.fire({
                    icon: 'warning',
                    title: '<span style="color:#FF003C;"> Oops!',
                    text: 'Invalid password or email!',
                    footer: 'SeaJet International Inc.',
                });
            } 
            
            else {
                Swal.fire({
                    icon: 'warning',
                    title: response.message || "Login failed"
                });
            }
        }
    });

});