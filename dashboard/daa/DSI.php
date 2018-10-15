<!DOCTYPE html>
<html lang="en" data-ng-app="iconomi">


<!-- Mirrored from www.iconomi.net/dashboard/daa/DSI by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Oct 2018 19:46:52 GMT -->
<head>
    <base ></base>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="og:title" content="ICONOMI">
    <meta name="og:description" content="The ICONOMI Digital Assets Management Platform is a new and unique technical service that allows anyone from beginners to blockchain experts to invest in and manage digital assets.">
    <meta name="keywords" content="app, responsive, angular, bootstrap, dashboard, admin">
    <title data-ng-bind="::pageTitle()"></title>
    <!-- Bootstrap styles-->
    <link rel="stylesheet" href="../app/css/bootstrap.db711984.css">
    <!-- Application styles-->
    <link rel="stylesheet" href="../app/css/app.e7f594c9.css">
    <!-- Application styles-->
    <link rel="stylesheet" href="../app/css/style.26da5f03.css">
    <link rel="icon" href="../app/img/favicons/favicon.ce9f13de.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="../app/img/favicons/apple-touch-icon.815ca6e7.png">
    <link rel="icon" type="image/png" href="../app/img/favicons/favicon-32x32.a7559180.png" sizes="32x32">
    <link rel="icon" type="image/png" href="../app/img/favicons/favicon-16x16.df51185b.png" sizes="16x16">
    <meta data-version="3.25.4" />
</head>

<body data-ng-class="{
'layout-fixed' : app.layout.isFixed,
'aside-collapsed' : app.layout.isCollapsed,
'layout-boxed' : app.layout.isBoxed,
'layout-fs': app.useFullLayout,
'hidden-footer': app.hiddenFooter,
'layout-h': app.layout.horizontal,
'aside-float': app.layout.isFloat,
'offsidebar-open': app.offsidebarOpen,
'aside-toggled': app.asideToggled,
'popup__overlay' : app.layout.isStaticPage,
'no-socket-connection': !$root.hasSocketConnection(),
'not-logged-in': !$root.userIsAuthorized
}" class="{{app.layout.dynamicBodyClassName}}">

<div data-preloader></div>

<div data-ui-view="" data-autoscroll="false" class="wrapper ng-cloak"></div>
<noscript>
    <div class="popup__overlay popup__overlay--no-header">
        <div class="popup popup--small">
            <div class="popup-small__content initialLoaded">
                <i class="static-page__icon static-page__icon--no-js"></i>
                <h2 class="newst-h2 static-page__headline--browser-fail">JavaScript disabled</h2>
                <p class="static-page__description--no-js">Please <span class="static-page__highlight--no-js">enable JavaScript</span> to continue to the ICONOMI dashboard.</p>
            </div>
        </div>
    </div>
</noscript>

<div class="popup__overlay popup__overlay--no-header hidden" id="localStorageDisabledMessage">
    <div class="popup popup--small">
        <div class="popup-small__content initialLoaded">
            <i class="static-page__icon static-page__icon--privacy-mode-enabled"></i>
            <h2 class="newst-h2 static-page__headline--browser-fail">Privacy mode is enabled in your browser.</h2>
        </div>
    </div>
</div>
<script src="../app/js/config.js"></script>
<script src="../app/js/base.819e6068.js"></script>
<script src="../app/js/app.aa3fecaa.js"></script>
<!-- Matomo -->
<script type="text/javascript">
    if (config.is_development === false) {
        var _paq = _paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(["setDomains", ["*.iconomi.net"]]);
        _paq.push(["setDoNotTrack", true]);
        _paq.push(["disableCookies"]);
        _paq.push(['enableLinkTracking']);
        (function() {
            var u="http://icn-analytics.com/";
            _paq.push(['setTrackerUrl', u+'piwik.php']);
            _paq.push(['setSiteId', '1']);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
        })();
    }
</script>
<noscript><p><img src="https://icn-analytics.com/piwik.php?idsite=1&amp;rec=1&amp;action_name=no_script" style="border:0;" alt="" /></p></noscript>
<!-- End Matomo Code -->
</body>

<!-- Mirrored from www.iconomi.net/dashboard/daa/DSI by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Oct 2018 19:46:52 GMT -->
</html>
