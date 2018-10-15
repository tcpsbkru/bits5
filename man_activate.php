<?php
include 'dbc.php';
/******************* activation by form *******************/
// if ($_POST['doActivate']=='Activate')
if (filter_input(INPUT_POST, 'doActivate') === 'Activate') {
  echo  $user_email = mysqli_real_escape_string($link, $_POST ['user_email']);
    echo  $activation_code = mysqli_real_escape_string($link, $_POST ['activation_code']);

    // check if activ code and user is valid as precaution
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'SELECT id FROM users WHERE user_email=? AND activation_code=?')) {
        mysqli_stmt_bind_param($stmt, "si", $user_email, $activation_code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($id <= 0) {
        $msg = urlencode("Sorry no such account exists or activation code invalid.");
        header("Location: man_activate.php?msg=$msg");
        exit ();
    }
    // set approved field to 1 to activate the user
    $approved = 1;
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'UPDATE users SET approved=? WHERE user_email=? AND activation_code = ?')) {
        mysqli_stmt_bind_param($stmt, "isi", $approved, $user_email, $activation_code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $msg = urlencode("Thank you. Your account has been activated.");
    header("Location: login.php?msg=$msg");
    exit ();
}

?>
<!DOCTYPE html>

<html>
<head>

 <!-- Histats.com  START  (aync)-->    <script type="text/javascript">var _Hasync = _Hasync || [];        _Hasync.push(['Histats.start', '1,4027956,4,0,0,0,00010000']);        _Hasync.push(['Histats.fasi', '1']);        _Hasync.push(['Histats.track_hits', '']);        (function () {            var hs = document.createElement('script');            hs.type = 'text/javascript';            hs.async = true;            hs.src = ('//s10.histats.com/js15_as.js');            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);        })();</script>    <noscript><a href="/" target="_blank"><img src="//sstatic1.histats.com/0.gif?4027956&101" alt="stats counter"                                               border="0"></a></noscript>    <!-- Histats.com  END  --></head>

<body>
    <table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
    <td width="160" valign="top"><p>&nbsp;</p>
        <p>&nbsp; </p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p></td>
    <td width="732" valign="top">
    <h3 class="titlehdr">Account Activation</h3>
    <p>
        <?php
        if (isset ($_GET ['msg'])) {
            $msg = mysqli_real_escape_string($link, $_GET ['msg']);
            echo "<div class=\"msg\">$msg</div>";
        }
        ?>
    </p>
    <p>Please enter your email and activation code sent to your email address.</p>
    <form action="man_activate.php" method="post" name="actForm" id="actForm">
        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td width="36%">Your Email</td>
                <td width="64%"><input name="user_email" type="text" class="required email" id="txtboxn" size="25"></td>
            </tr>
            <tr>
                <td>Activation code</td>
                <td><input name="activation_code" type="password" class="required" id="txtboxn" size="25"></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div align="center">
                        <p>
                            <input name="doActivate" type="submit" id="doLogin3" value="Activate">
                        </p>
                    </div>
                </td>
            </tr>
        </table>
        <div align="center"></div>
        <p align="center">&nbsp; </p>
    </form>
</body>
</html>