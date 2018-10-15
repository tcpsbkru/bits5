<?php
include 'dbc.php';
if (filter_input(INPUT_POST, 'doRegister') === 'Register') {
    //printa(INPUT_POST);
    // Sanitizing
    foreach ($_POST as $key => $value) {
        $data [$key] = filter($link, $value);
    }
    // var_dump ( $data );
    // verify recaptcher
    $email;
    $comment;
    $captcha;
    if (isset ($data ['email'])) {
        $email = $data ['email'];
    }
    if (isset ($data ['comment'])) {
        $email = $data ['comment'];
    }
    if (isset ($data ['g-recaptcha-response'])) {
        $captcha = $data ['g-recaptcha-response'];
    }
    if (!$captcha) {
        echo '<h2>Please check the the captcha form.</h2>';
        exit ();
    }
    $secretKey = "6Lc8Fj4UAAAAAC06xnNQRSVSs2QC2emUwzqT7qn8";
    $ip = $_SERVER ['REMOTE_ADDR'];
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha . "&remoteip=" . $ip);
    $responseKeys = json_decode($response, true);
    if (intval($responseKeys ["success"]) !== 1) {
        echo '<h2>You are spammer ! Get the @$%K out</h2>';
    } else {
        // echo '<h2>Thanks for posting comment.</h2>';
    }

    // server side validation
    if (empty ($data ['full_name']) || strlen($data ['full_name']) < 3) {
        $err = urlencode("ERROR: Invalid name. Please enter atleast 3 or more characters for your name");
        header("Location: registration.php?err=$err");
        exit ();
    }

    // Validate User Name
    if (!isUserID($data ['user_name'])) {
        $err = urlencode("ERROR: Invalid user name. It can only contain alphabet character, number and underscore.");
        header("Location: registration.php?err=$err");
        exit ();
    }

    // Validate Email
    if (!isEmail($data ['user_email'])) {
        $err = urlencode("ERROR: Invalid email.");
        header("Location: registration.php?err=$err");
        exit ();
    }
    // Check User Passwords
    if (!checkPwd($data ['pwd'], $data ['pwd2'])) {
        $err = urlencode("ERROR: Invalid Password or mismatch. Enter 10 chars or more");
        header("Location: registration.php?err=$err");
        exit ();
    }

    $user_ip = $_SERVER ['REMOTE_ADDR'];

    // store md5 of password
    $md5pass = md5($data ['pwd']);

    // Automatically collects the hostname or domain like example.com)
    $host = $_SERVER ['HTTP_HOST'];
    $host_upper = strtoupper($host);
    $path = rtrim(dirname($_SERVER ['PHP_SELF']), '/\\');

    // Generate activation code simple 4 digit number
    $activation_code = rand(1000, 9999);

    $user_email = $data ['user_email'];
    $user_name = $data ['user_name'];
    $full_name = $data ['full_name'];
    $address = $data ['address'];
    $tel = $data ['tel'];
    $fax = $data ['fax'];
    $web = $data ['web'];
    $country = $data ['country'];

    // check on the server side if the email already exists
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'SELECT count(*) AS total FROM users WHERE user_email=? OR user_name=?')) {
        mysqli_stmt_bind_param($stmt, "ss", $user_email, $user_name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($total > 0) {
        $err = urlencode("ERROR: The username/email already exists. Please try again with different username and email.");
        header("Location: registration.php?err=$err");
        exit ();
    }
    // check on the server side if the ip already exists
    $stmt = mysqli_stmt_init($link);
    if (mysqli_stmt_prepare($stmt, 'SELECT count(*) AS total FROM users WHERE user_ip=?')) {
        mysqli_stmt_bind_param($stmt, "s", $user_ip);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $total);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    if ($total > 0) {
        $err = urlencode("ERROR: Only one IP address permited.");
        header("Location: registration.php?err=$err");
        exit ();
    }

    $stmt = mysqli_stmt_init($link);
    $sql = "INSERT INTO users (full_name, user_name, user_email, pwd, address, country, tel, fax, website, account_date, user_ip, activation_code)
	VALUES(?,?,?,?,?,?,?,?,?,now(),?,?)";
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssssssssi", $full_name, $user_name, $user_email, $md5pass, $address, $country, $tel, $fax, $web, $user_ip, $activation_code);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $user_id = mysqli_insert_id($link);
    $md5_id = md5($user_id);

    $stmt = mysqli_stmt_init($link);
    $sql = "UPDATE users SET md5_id=? WHERE id=?";
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $md5_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    // send email
    $message = "Thank you for registering with us. Here are your login details...\n
User ID: $user_name
Email: $user_email \n
Passwd: $data[pwd] \n
Activation code: $activation_code \n

*****ACTIVATION LINK*****\n



https://$host$path/login.php?md5_id=$md5_id&activation_code=$activation_code



Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE.
***DO NOT RESPOND TO THIS EMAIL****
";

    mail($user_email, "Login Details", $message,
        "From: \"Member Registration\" <auto-reply@$host>\r\n" . "X-Mailer: PHP/" . phpversion());

    header("Location: thankyou.php");

    exit ();
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

    <section autoscroll="false" class="ng-zoomBackDown ng-fluid ng-scope">
        <!-- ngInclude: 'app/views/partials/notification-banner.e9611998.html' -->
        <div ng-include="&#39;app/views/partials/notification-banner.e9611998.html&#39;" class="ng-scope" style="">
            <!-- ngIf: app.showNotificationBanner || !$root.hasSocketConnection() -->
        </div>
        <!-- uiView: -->
        <div ui-view="" class="content-wrapper ng-scope">
            <div class="popup popup--small ng-scope">
                <div class="initialLoaded">
                    <register-form class="ng-isolate-scope">
                        <div class="popup-small__headline">
                            <h2 class="newst-h2 static-page__form-headline">Register</h2>
                        </div>
                        <?php
                        if (isset ($_GET ['err'])) {
                            $err = mysqli_real_escape_string($link, $_GET ['err']);
                            echo "<div class=\"err\">$err</div>";
                        }
                        ?>
                        <div class="popup-small__content">
                            <form class="form-validate mb-lg ng-pristine ng-valid-email ng-invalid ng-invalid-required ng-invalid-recaptcha"
                                  action="registration.php" method="post"
                                  name="regForm" id="regForm" onsubmit="return validateForm()">

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Name</label>
                                    <input
                                            class="form-control newst-input ng-pristine ng-isolate-scope ng-empty ng-valid-email ng-invalid ng-invalid-required ng-touched"
                                            type="text" id="full_name" size="40" minlength="3" name="full_name"
                                            required/>
                                </div>

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Address</label>

                                    <textarea name="address" cols="40" rows="4" id="address"
                                              class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                              minlength="3"
                                              required></textarea>
                                </div>
                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Country </label>
                                    <select name="country"
                                            class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                            id="select8" required>
                                        <option value="" selected></option>
                                        <option value="Afghanistan">Afghanistan</option>
                                        <option value="Albania">Albania</option>
                                        <option value="Algeria">Algeria</option>
                                        <option value="Andorra">Andorra</option>
                                        <option value="Anguila">Anguila</option>
                                        <option value="Antarctica">Antarctica</option>
                                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Armenia ">Armenia</option>
                                        <option value="Aruba">Aruba</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Austria">Austria</option>
                                        <option value="Azerbaidjan">Azerbaidjan</option>
                                        <option value="Bahamas">Bahamas</option>
                                        <option value="Bahrain">Bahrain</option>
                                        <option value="Bangladesh">Bangladesh</option>
                                        <option value="Barbados">Barbados</option>
                                        <option value="Belarus">Belarus</option>
                                        <option value="Belgium">Belgium</option>
                                        <option value="Belize">Belize</option>
                                        <option value="Bermuda">Bermuda</option>
                                        <option value="Bhutan">Bhutan</option>
                                        <option value="Bolivia">Bolivia</option>
                                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                                        <option value="Brazil">Brazil</option>
                                        <option value="Brunei">Brunei</option>
                                        <option value="Bulgaria">Bulgaria</option>
                                        <option value="Cambodia">Cambodia</option>
                                        <option value="Canada">Canada</option>
                                        <option value="Cape Verde">Cape Verde</option>
                                        <option value="Cayman Islands">Cayman Islands</option>
                                        <option value="Chile">Chile</option>
                                        <option value="China">China</option>
                                        <option value="Christmans Islands">Christmans Islands</option>
                                        <option value="Cocos Island">Cocos Island</option>
                                        <option value="Colombia">Colombia</option>
                                        <option value="Cook Islands">Cook Islands</option>
                                        <option value="Costa Rica">Costa Rica</option>
                                        <option value="Croatia">Croatia</option>
                                        <option value="Cuba">Cuba</option>
                                        <option value="Cyprus">Cyprus</option>
                                        <option value="Czech Republic">Czech Republic</option>
                                        <option value="Denmark">Denmark</option>
                                        <option value="Dominica">Dominica</option>
                                        <option value="Dominican Republic">Dominican Republic</option>
                                        <option value="Ecuador">Ecuador</option>
                                        <option value="Egypt">Egypt</option>
                                        <option value="El Salvador">El Salvador</option>
                                        <option value="Estonia">Estonia</option>
                                        <option value="Falkland Islands">Falkland Islands</option>
                                        <option value="Faroe Islands">Faroe Islands</option>
                                        <option value="Fiji">Fiji</option>
                                        <option value="Finland">Finland</option>
                                        <option value="France">France</option>
                                        <option value="French Guyana">French Guyana</option>
                                        <option value="French Polynesia">French Polynesia</option>
                                        <option value="Gabon">Gabon</option>
                                        <option value="Germany">Germany</option>
                                        <option value="Gibraltar">Gibraltar</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Greece">Greece</option>
                                        <option value="Greenland">Greenland</option>
                                        <option value="Grenada">Grenada</option>
                                        <option value="Guadeloupe">Guadeloupe</option>
                                        <option value="Guatemala">Guatemala</option>
                                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                                        <option value="Guinea">Guinea</option>
                                        <option value="Haiti">Haiti</option>
                                        <option value="Honduras">Honduras</option>
                                        <option value="Hong Kong">Hong Kong</option>
                                        <option value="Hungary">Hungary</option>
                                        <option value="Iceland">Iceland</option>
                                        <option value="India">India</option>
                                        <option value="Indonesia">Indonesia</option>
                                        <option value="Ireland">Ireland</option>
                                        <option value="Israel">Israel</option>
                                        <option value="Italy">Italy</option>
                                        <option value="Jamaica">Jamaica</option>
                                        <option value="Japan">Japan</option>
                                        <option value="Jordan">Jordan</option>
                                        <option value="Kazakhstan">Kazakhstan</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Kiribati ">Kiribati</option>
                                        <option value="Kuwait">Kuwait</option>
                                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                                        <option value="Lao People's Democratic Republic">Lao People's
                                            Democratic Republic
                                        </option>
                                        <option value="Latvia">Latvia</option>
                                        <option value="Lebanon">Lebanon</option>
                                        <option value="Liechtenstein">Liechtenstein</option>
                                        <option value="Lithuania">Lithuania</option>
                                        <option value="Luxembourg">Luxembourg</option>
                                        <option value="Macedonia">Macedonia</option>
                                        <option value="Madagascar">Madagascar</option>
                                        <option value="Malawi">Malawi</option>
                                        <option value="Malaysia ">Malaysia</option>
                                        <option value="Maldives">Maldives</option>
                                        <option value="Mali">Mali</option>
                                        <option value="Malta">Malta</option>
                                        <option value="Marocco">Marocco</option>
                                        <option value="Marshall Islands">Marshall Islands</option>
                                        <option value="Mauritania">Mauritania</option>
                                        <option value="Mauritius">Mauritius</option>
                                        <option value="Mexico">Mexico</option>
                                        <option value="Micronesia">Micronesia</option>
                                        <option value="Moldavia">Moldavia</option>
                                        <option value="Monaco">Monaco</option>
                                        <option value="Mongolia">Mongolia</option>
                                        <option value="Myanmar">Myanmar</option>
                                        <option value="Nauru">Nauru</option>
                                        <option value="Nepal">Nepal</option>
                                        <option value="Netherlands Antilles">Netherlands Antilles</option>
                                        <option value="Netherlands">Netherlands</option>
                                        <option value="New Zealand">New Zealand</option>
                                        <option value="Niue">Niue</option>
                                        <option value="North Korea">North Korea</option>
                                        <option value="Norway">Norway</option>
                                        <option value="Oman">Oman</option>
                                        <option value="Pakistan">Pakistan</option>
                                        <option value="Palau">Palau</option>
                                        <option value="Panama">Panama</option>
                                        <option value="Papua New Guinea">Papua New Guinea</option>
                                        <option value="Paraguay">Paraguay</option>
                                        <option value="Peru ">Peru</option>
                                        <option value="Philippines">Philippines</option>
                                        <option value="Poland">Poland</option>
                                        <option value="Portugal ">Portugal</option>
                                        <option value="Puerto Rico">Puerto Rico</option>
                                        <option value="Qatar">Qatar</option>
                                        <option value="Republic of Korea Reunion">Republic of Korea
                                            Reunion
                                        </option>
                                        <option value="Romania">Romania</option>
                                        <option value="Russia">Russia</option>
                                        <option value="Saint Helena">Saint Helena</option>
                                        <option value="Saint kitts and nevis">Saint kitts and nevis</option>
                                        <option value="Saint Lucia">Saint Lucia</option>
                                        <option value="Samoa">Samoa</option>
                                        <option value="San Marino">San Marino</option>
                                        <option value="Saudi Arabia">Saudi Arabia</option>
                                        <option value="Seychelles">Seychelles</option>
                                        <option value="Singapore">Singapore</option>
                                        <option value="Slovakia">Slovakia</option>
                                        <option value="Slovenia">Slovenia</option>
                                        <option value="Solomon Islands">Solomon Islands</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="Spain">Spain</option>
                                        <option value="Sri Lanka">Sri Lanka</option>
                                        <option value="St.Pierre and Miquelon">St.Pierre and Miquelon</option>
                                        <option value="St.Vincent and the Grenadines">St.Vincent and the
                                            Grenadines
                                        </option>
                                        <option value="Sweden">Sweden</option>
                                        <option value="Switzerland">Switzerland</option>
                                        <option value="Syria">Syria</option>
                                        <option value="Taiwan ">Taiwan</option>
                                        <option value="Tajikistan">Tajikistan</option>
                                        <option value="Thailand">Thailand</option>
                                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                                        <option value="Turkey">Turkey</option>
                                        <option value="Turkmenistan">Turkmenistan</option>
                                        <option value="Turks and Caicos Islands">Turks and Caicos
                                            Islands
                                        </option>
                                        <option value="Ukraine">Ukraine</option>
                                        <option value="UAE">UAE</option>
                                        <option value="UK">UK</option>
                                        <option value="USA">USA</option>
                                        <option value="Uruguay">Uruguay</option>
                                        <option value="Uzbekistan">Uzbekistan</option>
                                        <option value="Vanuatu">Vanuatu</option>
                                        <option value="Vatican City">Vatican City</option>
                                        <option value="Vietnam">Vietnam</option>
                                        <option value="Virgin Islands (GB)">Virgin Islands (GB)</option>
                                        <option value="Virgin Islands (U.S.) ">Virgin Islands (U.S.)</option>
                                        <option value="Wallis and Futuna Islands">Wallis and Futuna
                                            Islands
                                        </option>
                                        <option value="Yemen">Yemen</option>
                                        <option value="Yugoslavia">Yugoslavia</option>
                                    </select required>
                                </div>
                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Phone</label>
                                    <input name="tel" type="text" id="tel"
                                           class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required">
                                </div>

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">User name</label>
                                    <input name="user_name" type="text" id="user_name"
                                           class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                           minlength="5" required>
                                    <br>
                                    <input name="btnAvailable" type="button" id="btnAvailable" onclick=
                                    '$("#checkid").html("Please wait...");
                                $.get("checkuser.php", {cmd: "check", user: $("#user_name").val()},
                                function (data) {
                                    $("#checkid").html(data);
                                });' value="Check Availability">

                                    <span style="color: red; font: bold 12px verdana;" id="checkid"></span>
                                </div>

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Email</label>

                                    <input name="user_email" type="email" id="user_email3"
                                           class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                           required/>
                                </div>

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Password</label>
                                    <input name="pwd" type="password"
                                           class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                           minlength="1" id="pwd" required>
                                </div>
                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Confirm password</label>
                                    <input name="pwd2" id="pwd2"
                                           class="form-control newst-input ng-pristine ng-untouched ng-empty ng-invalid ng-invalid-required"
                                           type="password" minlength="1" equalto="#pwd" required>
                                </div>

                                <div class="form-group static-page__form-group">
                                    <div class="checkbox static-page-agree-terms__container">
                                        <label>
                                            <input type="checkbox" value="true" name="terms" id="terms1"
                                                   ng-model="$ctrl.account.terms"
                                                   class="static-page-agree-terms__checkbox ng-pristine ng-untouched ng-valid ng-empty"
                                                   style="" required>
                                            <span>I agree to the <a href="https://www.gvb.net/terms-of-use"
                                                                    target="_blank" class="">Terms of Use</a></span>
                                        </label>
                                        <ul class="errorList"></ul>
                                    </div>
                                </div>

                                <div class="form-group has-feedback static-page__form-group">
                                    <label class="control-label newst-label">Image Verification</label>
                                    <div class="g-recaptcha"
                                         data-sitekey="6Lc8Fj4UAAAAACNo8gV5xijRmncClcC7RgoJcPti"></div>
                                </div>


                                <input class="button button--block button--primary" name="doRegister" type="submit"
                                       id="doRegister"
                                       value="Register">

                                <div class="static-page__more-links">
                                    <div class="static-page-more-links__item">
                                        Already have an account?
                                        <a ui-sref="page.login" class="text-button smaller-font"
                                           href="login.php">Login.</a>
                                    </div>
                                </div>

                            </form>

                    </register-form>
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
