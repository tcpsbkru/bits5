<?php
$url = $_SERVER ['REQUEST_URI'];
header ( "Refresh: 600; URL=$url" );
include 'dbc.php';
page_protect();
if (isset ($_GET ['id'])) {
    $id = mysqli_real_escape_string($link, $_GET ['id']);
}

/* create a prepared statement http://php.net/manual/en/mysqli-stmt.prepare.php */
$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT address, expected_satoshis FROM bits WHERE id=?')) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $address, $expected_satoshis);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
$expected_satoshis = satoshi_to_btc($expected_satoshis);
$qr = "bitcoin:" . $address . "?amount=" . $expected_satoshis;

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
    <section autoscroll="false" class="ng-zoomBackDown ng-fluid ng-scope">
        <!-- ngInclude: 'app/views/partials/notification-banner.e9611998.html' -->
        <div ng-include="&#39;app/views/partials/notification-banner.e9611998.html&#39;" class="ng-scope">
            <!-- ngIf: app.showNotificationBanner || !$root.hasSocketConnection() -->
        </div>
        <!-- uiView: -->
        <div ui-view="" class="content-wrapper ng-scope" style="">
            <div class="popup popup--small ng-scope">
                <div class="initialLoaded">
                    <login-form class="ng-isolate-scope"><!-- ngInclude: -->
                        <ng-include src="$ctrl.template" class="ng-scope">
                            <div class="popup-small__headline ng-scope">
                                <h2 class="newst-h2 static-page__form-headline">Scan this QR with your wallet application</h2>
                            </div>
                            <div class="popup-small__content ng-scope">

                                <img src="qr/php/qr_img.php?d=<?= $qr ?>&s=6">
                                <h4><p><?= $address ?></p></h4>
                                <h4><p><?= $expected_satoshis ?> BTC</p></h4>

                            </div>
                        </ng-include>
                    </login-form>
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
