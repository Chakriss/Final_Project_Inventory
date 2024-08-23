<?php
    session_start();
    include_once 'config/connect_db.php';
    include_once 'header.php';

    // Check the user level and include relevant files
    if (isset($_SESSION["user_level"]) && $_SESSION["user_level"] === "Admin") {
        include_once 'menu_admin.php';
    } else {
        include_once 'menu_user.php';
    }
    include_once 'navbar.php';

?>



<?php
    include_once 'footer.php';
?>
