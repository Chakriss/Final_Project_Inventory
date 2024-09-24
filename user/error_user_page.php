<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">

    <link rel="stylesheet" href="../assets/vendors/iconly/bold.css">

    <link rel="stylesheet" href="../assets/vendors/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/app.css">
    <link rel="shortcut icon" href="../assets/images/logo/optinova.jpg" type="image/x-icon">
    
</head>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@latest"></script>
<style>
    /* Custom styles for SweetAlert2 buttons */
    .swal2-confirm {
        background-color: #007bff !important;
        /* Red background color for the confirm button */
        color: white !important;
        /* White text color */
    }

    .swal2-cancel {
        background-color: red !important;
        /* Default color for the cancel button (Bootstrap primary color) */
        color: white !important;
        /* White text color */
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!isset($_SESSION["user_level"]) || $_SESSION["user_level"] !== "U") { ?>
            Swal.fire({
                title: "Access Denied",
                text: "You do not have permission to view this page.",
                icon: "error",
                showCancelButton: true,
                cancelButtonText: 'Logout',
                confirmButtonText: 'Home',
                customClass: {
                    cancelButton: 'swal2-cancel',
                    confirmButton: 'swal2-confirm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to admin_page.php
                    window.location.href = '../admin/admin_page.php';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Redirect to logout.php
                    window.location.href = '../logout.php';
                }
            });
        <?php } ?>
    });
</script>