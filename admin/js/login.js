$(document).ready(function () {

    $('#btnLogin').on('click', doLogin);

    $('#frmLogin').on('keypress', function (e) {
        if (e.which === 13) doLogin();
    });

    function doLogin() {
        var email = $.trim($('#txtUname').val());
        var pass  = $.trim($('#txtPass').val());

        if (!email || !pass) {
            Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'Please enter your email and password.' });
            return;
        }

        $('#btnLogin').prop('disabled', true).text('Signing in…');

        $.ajax({
            url: 'php_files/login.php',
            type: 'POST',
            dataType: 'json',
            data: $('#frmLogin').serialize(),
            success: function (res) {
                if (res.statusCode === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome back!',
                        text: 'Redirecting to dashboard…',
                        timer: 1000,
                        showConfirmButton: false
                    }).then(function () {
                        window.location.href = '../dashboard.php';
                    });
                } else if (res.statusCode === 201) {
                    showError('Missing fields', 'Please fill out all required fields.');
                } else if (res.statusCode === 202) {
                    showError('Incorrect password', 'Please check your credentials and try again.');
                } else if (res.statusCode === 203) {
                    showError('Account not found', 'No active account found with that email.');
                } else {
                    showError('Login failed', res.message || 'An unexpected error occurred.');
                }
            },
            error: function () {
                showError('Server error', 'Could not reach the server. Please try again.');
            },
            complete: function () {
                $('#btnLogin').prop('disabled', false).text('Sign In');
            }
        });
    }

    function showError(title, text) {
        Swal.fire({ icon: 'error', title: title, text: text });
    }

});
