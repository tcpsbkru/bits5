<?php
include 'dbc.php';

if (isset ($_GET ['cmd']) && $_GET ['cmd'] == 'check') {

    $user = mysqli_real_escape_string($link, $_GET ['user']);

    if (empty ($user) || strlen($user) <= 3) {
        echo "Enter 5 chars or more";
        exit ();
    }

    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'SELECT count(*) AS total FROM users WHERE user_name=?')) {
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
    if ($total > 0) {
        echo "Not Available";
    } else {
        echo "Available";
    }
}

?>