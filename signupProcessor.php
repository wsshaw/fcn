<?php
	/**
	 * User registration processing for Fantasy Collecting
	 * 
	 * Processes new user registration attempts from the signup form, validates
	 * input data, checks for username availability, and creates new user accounts.
	 * Includes basic input validation and error handling.
	 *
	 * @package    FantasyCollecting
	 * @author     William Shaw <william.shaw@duke.edu>
	 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
	 * @version    0.2 (modernized)
	 * @since      2012-10-01 (original), 2025-09-10 (modernized)
	 * @license    MIT
	 * 
	 * @param string $_POST['name']             The requested username
	 * @param string $_POST['password']         Password (first entry)
	 * @param string $_POST['confirm_password'] Password (validation entry)
	 * @param string $_POST['email']            User's email address
	 * @param bool   $_POST['ok_to_use_record'] User consent for research data usage
	 */
	$r_username = $_POST['name'];
	$r_password = $_POST['password'];
	$r_password_confirm = $_POST['confirm_password'];
	$r_email = $_POST['email'];
	$r_record = $_POST['ok_to_use_record'];

        require_once 'game/db.php';

        ob_start( );
                require 'new/functions.php';
        ob_end_clean( );
?>
<html>
<head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script><script type="text/javascript">
        google.load( "jquery", "1" );
        google.load( "jqueryui", "1" );
</script>
<link rel="stylesheet" type="text/css" href="new/fcn.css"/>
<link rel="stylesheet" type="text/css" href="new/jquery-ui.css"/>
<body style="background-color:#fff;font-size:1em;">
<div style="width:50%;margin-left:auto;margin-right:auto;">
<h1>Signup Results</h1>
<?php
	// Possible improvement: all this validation really could be happening via JavaScript 
	// before form submission. 

	if ( !isUsernameAvailable( $r_username ) ) {
		// See if the username is taken
		echo( "Sorry, but the collector name <b>" . $r_username . "</b> is already taken.  Please go " );
		echo( "<a href=\"javascript:window.history.back();\">back</a> and try again." );
	} elseif ( strlen( $r_password ) < 6 ) {
		// Minimum password length is 6 characters.  Please fix this magic number
		echo( "Your password must be at least 6 characters long.  Please go " );
		echo( "<a href=\"javascript:window.history.back();\">back</a> and try again." );
	} elseif ( strcmp( $r_password, $r_password_confirm ) != 0 ) {
		// Compare the password and the confirmation password...
		echo( "Sorry, but your passwords don't match.  Please go " );
		echo( "<a href=\"javascript:window.history.back();\">back</a> and try again." );
	} elseif ( !isEmailValid( $r_email ) ) {
		// Call the isEmailValid() function (defined in functions.php) to see if the email
		// matches regexps for a valid address.
		echo( "Invalid email address.  Please go " );
		echo( "<a href=\"javascript:window.history.back();\">back</a> and try again." );
	} else { 
		// Okay, form checks out
		createUser( $r_username, $r_password, $r_email, $r_record );
		echo( "Welcome to Fantasy Collecting!  You can now <a href=\"javascript:window.parent.Shadowbox.close();\">Log in</a> and have a look around." );
	}
?>
</div>
</body>
</html>
