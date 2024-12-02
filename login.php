<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Optinova</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="shortcut icon" href="assets/images/logo/optinova.jpg" type="image/x-icon">
</head>
<style>
    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>

<body>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <h1 class="auth-title">Log in.</h1>
                    <form id="loginForm" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" id="email" name="email" placeholder="Username">
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" id="password" name="password" placeholder="Password">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div class="toggle-password" onclick="togglePassword('password')">
                                <i class="bi bi-eye" id="togglePasswordIcon-password"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" id="top-center" onclick="login()">Log in</button>
                    </form>
                    <!-- <div class="text-center mt-5 text-lg fs-4">
                        <p><a class="font-bold" href="forgot_password.php">Forgot password?</a>.</p>
                    </div> -->
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <!-- Optional content -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
    <!-- <script src="assets/js/main.js"></script> -->

    <script>
        function login() {
            event.preventDefault();
            let isValid = true;

            // Reset validation messages
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Form validation checks
            if ($('#email').val() == "") {
                $('#email').addClass('is-invalid');
                isValid = false;
            }
            if ($('#password').val() == "") {
                $('#password').addClass('is-invalid');
                isValid = false;
            }


            if (isValid) {
                const formData = new FormData(document.getElementById('loginForm'));
                formData.append('code', 'xxxxx'); // Append the code to FormData

                $.ajax({
                    url: "/Final_Project/api/api_login.php",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status === "successfully") {
                            Swal.fire({
                                title: "Login successfully!",
                                text: "Welcome To Inventory Management System",
                                icon: "success",
                                timer: 1000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = result.level;
                            });
                        } else {
                            Swal.fire({
                                title: "Login!",
                                text: result.status,
                                icon: "error"
                            });
                        }
                    }
                });
            } else {
                Swal.fire({
                    title: "Login!",
                    html: "Please fill in all information completely.",
                    icon: "error"
                });
            }
        }

        function togglePassword(fieldId) {
            var passwordField = document.getElementById(fieldId);
            var toggleIcon = document.getElementById('togglePasswordIcon-' + fieldId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }
    </script>
</body>

</html>