<?php
include 'dbc.php';
echo "email: ";

//send email to someone who did not pay full amount
$owed_gvb = 0;
$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT user_id, owed_gvb FROM bits WHERE owed_gvb >?')) {
    mysqli_stmt_bind_param($stmt, "i", $owed_gvb);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $owed_gvb);

    while (mysqli_stmt_fetch($stmt)) {
        $bits[] = array($user_id, $owed_gvb);
    }
   // print_r($bits);
    $num = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);


    for ($i = 0; $i < $num; $i++) {
        $user_id = $bits [$i] [0];
        $owed_gvb = $bits [$i] [1];
        //$owed = satoshi_to_btc($bits [$i] [1]);

        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, 'SELECT user_email FROM users WHERE id=?')) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $user_email);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        $subject = "Pending payment";
        $message = "Your payment for " . $owed_gvb ." GVB is pending.";
        $host = $_SERVER ['HTTP_HOST'];
        mail($user_email, $subject, $message, "From: \"Debt department\" <debt@$host>\r\n" . "X-Mailer: PHP/" . phpversion());
    }
}
