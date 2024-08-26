<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Optinova</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="shortcut icon" href="assets/images/logo/optinova.jpg" type="image/x-icon">
</head>

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
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" id="top-center" onclick="login()">Log in</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p><a class="font-bold" href="auth-forgot-password.html">Forgot password?</a>.</p>
                    </div>
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
            let val_email = "";
            let val_password = "";

            if ($('#email').val() === "") {
                val_email = "Email is empty.";
            }
            if ($('#password').val() === "") {
                val_password = "Password is empty.";
            }

            if (!val_email && !val_password) {
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
                                icon: "success"
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
                    html: val_email + "<br>" + val_password,
                    icon: "error"
                });
            }
        }
    </script>
</body>

</html>