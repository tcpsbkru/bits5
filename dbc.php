<?php
// echo $_SERVER [SERVER_NAME];
if ($_SERVER ['SERVER_NAME'] === "bitscharity.com") {
	$link = mysqli_connect ( "db716215776.db.1and1.com", "dbo716215776", "Zaichik1!", "db716215776" );
	if (! $link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno () . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error () . PHP_EOL;
		exit ();
	}
} else if ($_SERVER ['SERVER_NAME'] === "localhost") {
	$link = mysqli_connect ( "localhost", "root", "", "dbase" );
	if (! $link) {
		echo "Error: Unable to connect to MySQL." . PHP_EOL;
		echo "Debugging errno: " . mysqli_connect_errno () . PHP_EOL;
		echo "Debugging error: " . mysqli_connect_error () . PHP_EOL;
		exit ();
	}
}
$admin_user = 'admin';
$admin_pass = 'Zaichik1';
function page_protect() {
	session_start ();
	
	// check for cookies
	if (isset ( $_COOKIE ['user_id'] ) && isset ( $_COOKIE ['user_name'] )) {
		$_SESSION ['user_id'] = $_COOKIE ['user_id'];
		$_SESSION ['user_name'] = $_COOKIE ['user_name'];
	}
	
	if (! isset ( $_SESSION ['user_id'] )) {
		header ( "Location: login.php" );
	}
}
function filter($link, $data) {
	$data = trim ( htmlentities ( strip_tags ( $data ) ) );
	
	if (get_magic_quotes_gpc ())
		$data = stripslashes ( $data );
	
	$data = mysqli_real_escape_string ( $link, $data );
	
	return $data;
}
function ChopStr($str, $len) {
	if (strlen ( $str ) < $len)
		return $str;
	
	$str = substr ( $str, 0, $len );
	if ($spc_pos = strrpos ( $str, " " ))
		$str = substr ( $str, 0, $spc_pos );
	
	return $str . "...";
}
function isEmail($email) {
	return preg_match ( '/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email ) ? TRUE : FALSE;
}
function isUserID($username) {
	if (preg_match ( '/^[a-z\d_]{5,20}$/i', $username )) {
		return true;
	} else {
		return false;
	}
}
function isURL($url) {
	if (preg_match ( '/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url )) {
		return true;
	} else {
		return false;
	}
}
function checkPwd($x, $y) {
	if (empty ( $x ) || empty ( $y )) {
		return false;
	}
	if (strlen ( $x ) < 1 || strlen ( $y ) < 1) {
		return false;
	}
	
	if (strcmp ( $x, $y ) != 0) {
		return false;
	}
	return true;
}
// string manipulation functions
function after($thiss, $inthat) {
	if (! is_bool ( strpos ( $inthat, $thiss ) )) {
		return substr ( $inthat, strpos ( $inthat, $thiss ) + strlen ( $thiss ) );
	}
}
function between($thiss, $that, $inthat) {
	return before ( $that, after ( $thiss, $inthat ) );
}
function before($thiss, $inthat) {
	return substr ( $inthat, 0, strpos ( $inthat, $thiss ) );
}
function found($haystack, $needle) {
	return (strpos ( $haystack, $needle ) !== false);
}
function cutString($str, $amount = 1, $dir = "right") {
	if (($n = strlen ( $str )) > 0) {
		if ($dir == "right") {
			$start = 0;
			$end = $n - $amount;
		} else if ($dir == "left") {
			$start = $amount;
			$end = $n;
		}
		
		return substr ( $str, $start, $end );
	} else {
		return false;
	}
}
// print array
function printa($sweet) {
	foreach ( $sweet as $key => $value ) {
		echo $key . "\t" . $value . "<br>";
	}
}
function satoshi_to_btc($value) {
	return bcdiv ( intval ( $value ), 100000000, 8 );
}
function confirmations($address) {
	$url = "https://test-insight.bitpay.com/api/addr/" . $address . "/utxo";
	// $url = "https://insight.bitpay.com/api/addr/" . $address . "/utxo";
	$contents = file_get_contents ( $url, $headers = false );
	$arr_json = json_decode ( $contents, true );
	$bool = true;
	//check if the array is empty and set $bool to false
    //otherwise foreach will skip and leave $bool set to true
	if (count ( $arr_json ) == 0) {
		$bool = false;
	}
	foreach ( $arr_json as &$value ) {
		if ($value ['confirmations'] < 6) {
			$bool = false;
		}
	}
	return $bool;
}

function satoshis($address) {
    $url = "https://test-insight.bitpay.com/api/addr/" . $address . "/utxo";
    // $url = "https://insight.bitpay.com/api/addr/" . $address . "/utxo";
    $contents = file_get_contents ( $url, $headers = false );
    $arr_json = json_decode ( $contents, true );
    $bool = true;
    if (count ( $arr_json ) == 0) {
        $bool = false;
    }
    foreach ( $arr_json as &$value ) {
        if ($value ['satoshis'] == 0) {
            $bool = false;
        }
    }
    return $bool;
}
function get_link($page, $link) {
	if ($page == $link)
		echo "active";
}
$page = $_SERVER ['PHP_SELF'];
$page = between ( "/", ".", $page );
// $page = rtrim ( dirname ( $_SERVER ['PHP_SELF'] ), '/\\' );

//count how man new addresses left
$payment = "new";
$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, 'SELECT count(*) AS total FROM bits WHERE payment=?')) {
    mysqli_stmt_bind_param($stmt, "s", $payment);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $newpayment);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}


?>