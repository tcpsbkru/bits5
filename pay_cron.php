<?php
include 'dbc.php';
echo "cron ";

$payment1 = "pending";
$payment2 = "confirmed_wrong_amount";
$payment3 = "unconfirmed";
$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT id, user_id, expected_satoshis, expected_gvb, actual_gvb,  owed_gvb,  payment, email_sent,address, paytime FROM bits WHERE payment=? OR payment=? OR payment=?')) {
    mysqli_stmt_bind_param($stmt, "sss", $payment1, $payment2, $payment3);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $user_id, $expected_satoshis, $expected_gvb, $actual_gvb, $owed_gvb, $payment, $email_sent, $address, $paytime);

    while (mysqli_stmt_fetch($stmt)) {
        $bits[] = array($id, $user_id, $expected_satoshis, $expected_gvb, $actual_gvb, $owed_gvb, $payment, $email_sent, $address, $paytime);
    }

    $num = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
}

for ($i = 0; $i < $num; $i++) {
    $id = $bits [$i] [0];
    $user_id = $bits [$i] [1];
    $expected_satoshis = $bits [$i] [2];
    $expected_gvb = $bits [$i] [3];
    $actual_gvb = $bits [$i] [4];
    $owed_gvb = $bits [$i] [5];
    $payment = $bits [$i] [6];
    $email_sent = $bits [$i] [7];
    $address = $bits [$i] [8];
    $paytime = $bits [$i] [9];

    // $url = "https://insight.bitpay.com/api/addr/" . $address . "/balance";
    $url = "https://test-insight.bitpay.com/api/addr/" . $address . "/balance";
    $actual_satoshis = file_get_contents($url, $headers = false);

//    if ((time() - strtotime($paytime)) > 36 && !satoshis($address)) {
//        $payment = "canceled";
//        $stmt = mysqli_stmt_init($link);
//        if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET payment=? WHERE id=?')) {
//            mysqli_stmt_bind_param($stmt, "si", $payment, $id);
//            mysqli_stmt_execute($stmt);
//            mysqli_stmt_close($stmt);
//        }
//    }
//
//    $stmt = mysqli_stmt_init($link);
//    if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET actual_satoshis=?, owed=? WHERE id=?')) {
//        mysqli_stmt_bind_param($stmt, "iii", $actual_satoshis, $owed, $id);
//        mysqli_stmt_execute($stmt);
//        mysqli_stmt_close($stmt);
//    }

    if (!satoshis($address)) {
        $url = "https://blockchain.info/tobtc?currency=USD&value=1";
        $usd_in_btc = file_get_contents($url, $headers = false);
        $usd_in_satoshi = $usd_in_btc * 100000000;
        $expected_satoshis = $expected_gvb * $usd_in_satoshi;

        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET expected_satoshis=? WHERE id=?')) {
            mysqli_stmt_bind_param($stmt, "ii", $expected_satoshis, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    if (confirmations($address) && $actual_satoshis >= $expected_satoshis) {
        $payment = "confirmed";
        $owed_gvb = 0;
        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET actual_gvb=?, owed_gvb=?,  payment=? WHERE id=?')) {
            mysqli_stmt_bind_param($stmt, "ddsi",  $expected_gvb, $owed_gvb, $payment, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($email_sent != "sent") {
            $stmt = mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, 'SELECT user_email FROM users WHERE id=?')) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $user_email);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }

            $subject = "payment confirmed";
            $message = "You will recieve " . $expected_gvb . " GVB.";
            $host = $_SERVER ['HTTP_HOST'];
            mail($user_email, $subject, $message, "From: \"Bits\" <bits@$host>\r\n" . "X-Mailer: PHP/" . phpversion());

            $email_sent = "sent";
            $stmt = mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET email_sent=? WHERE id=?')) {
                mysqli_stmt_bind_param($stmt, "si", $email_sent, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

    }
    if (confirmations($address) && $actual_satoshis < $expected_satoshis) {
        $actual_gvb = ($actual_satoshis / $expected_satoshis) * $expected_gvb;
        $owed_gvb = $expected_gvb - $actual_gvb;

        $url= "https://bitpay.com/api/rates/BTC/USD";
        $contents = file_get_contents($url, $headers = false);
        $arr_json = json_decode ( $contents, true );
        $btc_in_usd = $arr_json['rate'];
        $usd_in_btc = 1/$btc_in_usd;
        $usd_in_satoshi = $usd_in_btc * 100000000;0;

        $expected_satoshis = $owed_gvb * $usd_in_satoshi;

        $payment = "confirmed_wrong_amount";
        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET expected_satoshis=?, actual_gvb=?, owed_gvb=?,  payment=? WHERE id=?')) {
            mysqli_stmt_bind_param($stmt, "iddsi", $expected_satoshis, $actual_gvb, $owed_gvb, $payment, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        if ($email_sent != "sent_wrong_amount") {
            $stmt = mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, 'SELECT user_email FROM users WHERE id=?')) {
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $user_email);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }

            $subject = "owe from cron";
            $message = "Payment pending for  " . $owed_gvb . " GVB.";
            $host = $_SERVER ['HTTP_HOST'];
            mail($user_email, $subject, $message, "From: \"Debt department cron\" <debt@$host>\r\n" . "X-Mailer: PHP/" . phpversion());

            $email_sent = "sent_wrong_amount";
            $stmt = mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET email_sent=? WHERE id=?')) {
                mysqli_stmt_bind_param($stmt, "si", $email_sent, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

    }
    if (!confirmations($address) && satoshis($address)) {
        $payment = "unconfirmed";
        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET payment=? WHERE id=?')) {
            mysqli_stmt_bind_param($stmt, "si", $payment, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}