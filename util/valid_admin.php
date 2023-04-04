<?php
    // make sure the user is logged in as a valid administrator
    if (!isset($_SESSION['admin_id'])) {
        header('Location: ' . $app_path . 'admin/account/' );
    }
?>
