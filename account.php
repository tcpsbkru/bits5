<?php
$url = $_SERVER ['REQUEST_URI'];
header("Refresh: 600; URL=$url");
include 'dbc.php';
page_protect();
$user_id = $_SESSION ['user_id'];

// get exchange rate of USD to BTC. 1GVB = 1USD
//$url = "https://blockchain.info/tobtc?currency=USD&value=1";
//$usd_in_btc = file_get_contents($url, $headers = false);
//$usd_in_satoshi = $usd_in_btc * 100000000;

$url = "https://bitpay.com/api/rates/BTC/USD";
$contents = file_get_contents($url, $headers = false);
$arr_json = json_decode($contents, true);
$btc_in_usd = $arr_json['rate'];
$usd_in_btc = 1 / $btc_in_usd;
$usd_in_satoshi = $usd_in_btc * 100000000;

if (filter_input(INPUT_POST, 'pay_name') === 'pay') {
    // Sanitizing
    foreach ($_POST as $key => $value) {
        $data [$key] = filter($link, $value);
    }

    $cus_address = $data ['cus_address'];
    $expected_gvb = $data ['quantity'];
    $expected_satoshis = $expected_gvb * $usd_in_satoshi;

    $payment = "new";
    $limit = 1;
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'SELECT id FROM bits WHERE payment=? LIMIT ?')) {
        mysqli_stmt_bind_param($stmt, "si", $payment, $limit);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    $payment = "pending";
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'UPDATE bits SET user_id=?, expected_satoshis=?, expected_gvb=?, owed_gvb=?, payment=?, cus_address=?, paytime=now()  WHERE id=? ')) {
        mysqli_stmt_bind_param($stmt, "iiddssi", $user_id, $expected_satoshis, $expected_gvb, $expected_gvb, $payment, $cus_address, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: pay.php?id=$id");
}

if (filter_input(INPUT_POST, 'pay_owed_name') === 'pay') {
// Sanitizing
    foreach ($_POST as $key => $value) {
        $data [$key] = filter($link, $value);
    }
    $id = $data ['id'];
    header("Location: pay.php?id=$id");
}

$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT id, expected_gvb, owed_gvb, payment, address, paytime FROM bits WHERE user_id=?')) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $expected_gvb, $owed_gvb, $payment, $address, $paytime);

    while (mysqli_stmt_fetch($stmt)) {
        $transactions[] = array($id, $expected_gvb, $owed_gvb, $payment, $address, $paytime);
    }

    $num_of_transactions = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
}

$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT SUM(actual_gvb) FROM bits WHERE user_id=?')) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>

  
<!-- Mirrored from www.iconomi.net/about by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Oct 2018 19:46:43 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="img/favicons/favicon.php">
    
    <title>ICONOMI - About us</title>

    <meta name="csrf-param" content="authenticity_token" />
<meta name="csrf-token" content="6fSt4c9I94mwsI6zg3g4hujuz9NdL56Lb60wGtUtu5jjFTTgLrInLRLlVfesKz3ocICI12ZcfpHmzAoMcMv1ig==" />

    <link rel="stylesheet" media="all" href="assets/application-d5d2e2aa23cac4ccc47cc6678f708ed694c0588385d29ded00b3123cd6a54c80.css" data-turbolinks-track="reload" />
    <script src="assets/application-54ff2d1a13255b89f052a189f90c30b31052406dac99d4f6e3b0a5bc02028f95.js" data-turbolinks-track="reload"></script>

    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffffff">

    <meta property='og:title' content="" />
    <meta property='og:description' content="" />
    <meta property='og:image' content="" />
    <meta property='og:url' content="" />
    <meta property='og:type' content="" />
    <link rel="stylesheet" href="./registration_files/style.6ad9f356.css">
</head>

  <body>
    <!-- Matomo -->
  <script type="text/javascript">
    var _paq = _paq || [];
    /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
    _paq.push(["setDomains", ["*.iconomi.net"]]);
    _paq.push(["setDoNotTrack", true]);
    _paq.push(["disableCookies"]);
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u="http://icn-analytics.com/";
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['setSiteId', '1']);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
      g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
  </script>
  <noscript><p><img src="https://icn-analytics.com/piwik.php?idsite=1&amp;rec=1&amp;action_name=no_script" style="border:0;" alt="" /></p></noscript>
  <!-- End Matomo Code -->


	<nav role="navigation" class="navbar topnavbar navbar-fixed-top ">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a href="index.php" class="navbar-brand">
                <div class="brand-logo">
                    <img src="assets/logos/logo-30ed55eb8048292e903f9a531a5e0fa75a3b3a21686c53e9d2338dfae180977f.svg" alt="Iconomi"
                         class="img-responsive logo__default"/>
                    <img src="assets/logos/logoWhite-c83c71c0ee9a716907493b54f968ca6ccb15bde372ce082c2e147de02d7ab3c0.svg" alt="Iconomi"
                         class="img-responsive logo__white"
                         style="display: none;"/>
                </div>
            </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <div class="nav-wrapper">
                <ul class="nav navbar-nav navbar--landing">
                    <li class="">
                        <a href="digital-assets.php">
                            Digital assets
                        </a>
                    </li>

                    <li class="">
                        <a href="arrays.php">
                            What is a DAA?
                        </a>
                    </li>

                    <li class="">
                        <a href="daa-list.php" class="navbar-link--daa-list">
                            DAA List
                        </a>
                    </li>

                    <li class="active">
                        <a href="about.php">
                            About us
                        </a>
                    </li>


                    <li class="">
                        <a href="events.php">
                            Events
                        </a>
                    </li>

                    <li>
                        <a href="https://medium.com/iconominet" target="_blank">
                            Blog
                        </a>
                    </li>

                    <li class="topnavbar-register pull-right">
                        <a href="registration.php" class="btn navbar__register-link">
                            Register
                        </a>
                    </li>

                    <li class="topnavbar-login pull-right">
                        <a href="login.php">
                            Login
                        </a>
                    </li>

                    <li class="topnavbar-dashboard pull-right">
                        <a href="dashboard/index.php" class="btn navbar__dashboard-link">
                            Dashboard
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>


	
<div class="content page-about">

    <section class="hero hero--about page-hero">
        <h1>About us</h1>
    </section>

    <section class="about-GVB">
        <div class="container">
            <div class="about-GVB__inner-wrap">
                <h2>Welcome <?php echo $_SESSION ['user_name']; ?></h2>
                To send BTC, you can use any private or exchange wallet.
                This is your unique deposit address. Once funds are received in
                this address your balance will be updated shortly.
                <p> PAY WITH QR CODE OR PAY TO ADDRESS BELOW USING YOUR WALLET APP</p>
                <h2><p> Your total is <?= $total ?> GVB</p></h2>
                <!--               <img src="img/Bitcoin_accepted_here_printable-1.png" class="about-GVB__icon">-->
            </div>
        </div>
    </section>
    <section class="login_section">
        <div class="container">
            <div class="row">
                <div class="col-md-12 page_title_div text-center">

                    <div class="divider"></div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-10 col-md-offset-1">
                    <div class="divbg_innerpage">

                        <div ui-view="" class="content-wrapper ng-scope" style="">
                            <div class="popup popup--small ng-scope">
                                <div class="initialLoaded">
                                    <login-form class="ng-isolate-scope"><!-- ngInclude: -->
                                        <ng-include src="$ctrl.template" class="ng-scope">
                                            <div class="popup-small__headline ng-scope">
                                                <p>
                                                <h2 class="newst-h2 static-page__form-headline">
                                                    Buy GVB tokens</h2></p>
                                                <p>1 USD = <?= $usd_in_btc ?> BTC</p>
                                                <p>1 BTC = <?= 1 / $usd_in_btc ?> USD</p>
                                                <?php
                                                if (isset($_GET['msg'])) {
                                                    $msg = mysqli_real_escape_string($link, $_GET['msg']);
                                                    echo "<div class=\"msg\">$msg</div>";
                                                }
                                                ?>

                                            </div>

                                            <div class="popup-small__content ng-scope">
                                                <form action="account.php" method="post"
                                                      name="logForm" id="logForm"
                                                      class="form-validate mb-lg ng-pristine ng-valid-email ng-invalid ng-invalid-required">
                                                    <div class="form-group has-feedback static-page__form-group">
                                                        <label class="control-label newst-label">Please
                                                            enter your ETH
                                                            wallet address</label>
                                                        <input name="cus_address"
                                                               type="text" id="txtbox"
                                                               class="form-control newst-input ng-pristine ng-isolate-scope ng-empty ng-valid-email ng-invalid ng-invalid-required ng-touched">
                                                    </div>
                                                    <div class="form-group has-feedback static-page__form-group">
                                                        <label class="control-label newst-label">Choose
                                                            quantity of GVB you wish to
                                                            buy</label>
                                                        <input name="quantity" type="text"
                                                               id="txtbox"
                                                               class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required">
                                                    </div>
                                                    <input class="button button--block button--primary"
                                                           name="pay_name" type="submit"
                                                           id="pay" value="pay">
                                                </form>
                                            </div>
                                        </ng-include>
                                    </login-form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="divbg_innerpage">
                        <h3>Transaction History</h3>
                        <table class="account-verification__table" width="100%">
                            <tr>
                                <td>
                                    <p><label>Date</label></p>
                                </td>
                                <td>
                                    <p><label>GVB</label></p>
                                </td>

                                <td>
                                    <p><label>Outstanding</label></p>
                                </td>
                                <td>
                                    <p><label>Transaction</label></p>
                                </td>
                                <td>
                                    <p><label>Address</label></p>
                                </td>
                                <td>
                                    <p><label>&nbsp&nbsp&nbspPay&nbsp&nbsp&nbsp</label></p>
                                </td>
                            </tr>
                            <?php
                            for ($i = 0; $i < $num_of_transactions; $i++) {
                                // $id, $expected_gvb, $owed_gvb, $payment, $address, $paytime
                                $date = date_create($transactions [$i] [5]);
                                $date = date_format($date, 'd/m/Y');
                                $expected_gvb = $transactions [$i] [1];
                                $payment = $transactions [$i] [3];
                                $owed_gvb = $transactions [$i] [2];
                                $address = $transactions [$i] [4];
                                $id = $transactions [$i] [0];
                                ?>
                                <tr>
                                    <td class="account-verification-table__verification-step">
                                        <p> <?= $date ?></p>
                                    </td>
                                    <td class="account-verification-table__verification-step">
                                        <!--                                        <p>  -->
                                        <? //= round($expected_gvb, 2) . " GVB " ?><!--</p>-->
                                        <p>  <?= $expected_gvb . " GVB " ?></p>
                                    </td>


                                    <td class="account-verification-table__verification-step">
                                        <p>  <?= $owed_gvb . " GVB " ?></p>

                                    </td>
                                    <td class="account-verification-table__verification-step">
                                        <p> <?php if ($payment == "confirmed" OR $payment == "confirmed_wrong_amount") {
                                                echo "confirmed";
                                            } else {
                                                echo $payment;
                                            } ?>
                                        </p>
                                    </td>
                                    <td class="account-verification-table__verification-step">
                                        <p> <?= $address ?>
                                        </p>
                                    </td>
                                    <td class="account-verification-table__verification-step">
                                        <p>
                                        <form class="login" action="account.php" method="post">
                                            <input name="id" type="hidden" value="<?= $id ?>">
                                            <?php
                                            if ($payment == "confirmed_wrong_amount" || $payment == "pending") { ?>
                                                <input class="button button--block button--primary"
                                                       name="pay_owed_name" type="submit"
                                                       value="pay">
                                            <?php } ?>
                                        </p>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>


    <footer>

    <div class="container">
        <div class="row footerRow">

            <div class="col-sm-6 col-md-6 col-xs-12 col-xs-push-6">
                <div class="row linksRow">

                    <div class="col-md-4 col-xs-4">
                        <ul class="footerLinks">
                            <li class="headerItem">
                                Support
                            </li>

                            <li>
                                <a href="https://iconomi.zendesk.com/" target="_blank">Help Center</a>
                            </li>
                            <li>
                                <a href="https://iconomi.zendesk.com/hc/en-us/requests/new" target="_blank">Contact</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-4 col-xs-4">
                        <ul class="footerLinks">
                            <li class="headerItem">
                                Company
                            </li>
                            <li>
                                <a href="about.php">About us</a>
                            </li>
                            <li>
                                <a href="press.php">Press</a>
                            </li>
                            <li>
                                <a href="jobs.php">Jobs</a>
                            </li>
                            <li>
                                <a href="events.php">Events</a>
                            </li>
                            <li>
                                <a href="https://medium.com/iconominet" target="_blank">Blog</a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-md-4 col-xs-4">
                        <ul class="footerLinks">
                            <li class="headerItem">
                                Legal
                            </li>
                            <li>
                                <a href="terms-of-use.php">Terms of Use</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-6 col-xs-12 col-xs-pull-6">
                <div class="brand-logo">
                    <img src="assets/logos/logo-30ed55eb8048292e903f9a531a5e0fa75a3b3a21686c53e9d2338dfae180977f.svg" alt="Iconomi"
                         class="img-responsive"/>
                </div>
                <div class="brand-text">
                    &copy; 2018 ICONOMI Inc. All rights reserved.
                </div>
                <div class="socialIconsContainer">
                    <a href="https://medium.com/iconominet" target="_blank"><span class="medium"></span></a>
                    <a href="https://www.facebook.com/iconomi.net" target="_blank"><span class="facebook"></span></a>
                    <a href="https://twitter.com/iconominet" target="_blank"><span class="twitter"></span></a>
                    <a href="https://www.linkedin.com/company/iconominet/" target="_blank"><span class="linkedin"></span></a>
                    <a href="https://www.reddit.com/r/ICONOMI" target="_blank"><span class="reddit"></span></a>
                    <a href="https://iconominet.rocket.chat/channel/announcements" target="_blank"><span class="rocketchat"></span></a>
                    <div class="footer__app-links-wrap">
                        <a href="https://play.google.com/store/apps/details?id=net.iconomi.android" target="_blank"><img src="assets/icons/iconGooglePlay-c879129e30e8c719aff6fbf2b8463a007f2511379c5f0ceb7864b2bb1de68ae7.svg" class="footer__app-link" alt="Google play icon" /></a>
                        <a href="https://itunes.apple.com/in/app/iconomi-digital-assets-management-platform/id1238213050" target="_blank"><img src="assets/icons/iconAppStore-312630ffcd8ffe969d6947e9b5cb231e80887c529ddb2939f995535a1c2c540c.svg" class="footer__app-link" alt="App store icon" /></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>


  </body>


<!-- Mirrored from www.iconomi.net/about by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Oct 2018 19:46:44 GMT -->
</html>
