<?php
/**
 * Login authentication handler for Fantasy Collecting
 * 
 * Processes login attempts with security features including input validation,
 * rate limiting, secure session management, and authentication verification.
 * Handles user authentication and redirects to appropriate game interface.
 * 
 * @package    FantasyCollecting
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 * @version    0.2 (modernized with security improvements)
 * @since      2012-08-01 (original), 2025-09-10 (modernized) 
 * @license    MIT
 * 
 * @param string $_POST['username'] The username from login form
 * @param string $_POST['password'] The password from login form
 */

	// Begin a secure session and include the database initializer
	require_once 'game/security.php';
	
	// Set secure session configuration
	ini_set('session.cookie_httponly', 1);
	ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
	ini_set('session.use_strict_mode', 1);
	session_start();
	
        require_once 'game/db.php';
	$gameinstance = -1;

?>
<html>
<head>
<title>User Frontend</title>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">

        google.load( "jquery", "1" );
        google.load( "jqueryui", "1" );

	function redirect( ) {
		window.location = 'game/home.php';
	}

</script>
<link rel="stylesheet" href="game/fcn.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="game/jquery-ui.css"/>
</head>
<body>
<?php
	$uuid = 0;
	$uname = "";
	
	// Validate input exists
	if (!isset($_POST['username']) || !isset($_POST['password'])) {
		echo "<p style='color: red;'>Missing login credentials.</p>";
		echo "<p><a href='index.php'>Try again</a></p>";
		exit();
	}
	
	$username = sanitize_string($_POST['username']);
	$password = $_POST['password']; // Keep raw for password verification
	
	// Rate limiting for login attempts
	if (!check_rate_limit('login', 5)) {
		echo "<p style='color: red;'>Too many login attempts. Please wait a minute.</p>";
		echo "<p><a href='index.php'>Try again</a></p>";
		exit();
	}

	// WARNING: This system still uses MD5 for backward compatibility with existing passwords
	// In a real modernization, you would need to migrate to password_hash()/password_verify()
	$stmt = $dbh->prepare( "SELECT id, name FROM collectors WHERE name = ? AND password = MD5(?)" );
	$stmt->execute([$username, $password]);
	$row = $stmt->fetch();
	
	if ($row)
	{ 
		$uuid = $row['id'];
		$uname = $row['name'];
	}
	else 
	{
		echo( "<h1>Invalid user or password.</h1>" );
		echo( "<p><a href='index.php'>Try again</a></p>" );
		echo ("</body></html>\n" );
		exit( );
	}

	// See if the user is playing more than one FC game.  This functionality is deprecated, but the code
	// remains here in case someone wants to re-implement it in the future.  
	$stmt = $dbh->prepare( "SELECT COUNT(*) as c,collections.id,owner,gameinstance,games.name as gn,games.ended,games.id FROM collections INNER JOIN games ON games.id = gameinstance WHERE owner = ? AND UNIX_TIMESTAMP( games.ended ) = 0" ); 
	$stmt->bindValue( 1, $uuid );
	$stmt->execute( );

	while( $row = $stmt->fetch( ) ) 
	{
		if( $row['c'] == "1" ) 
		{
			// Just one session -- easy enough	
			$gameinstance = $row[ 'gameinstance' ];
			break;
		}
		else if ( $row['c'] == "0" )
		{
			// No games.  TODO: write game selection logic.
			echo( "<h4>You aren't participating in any games!</h4>" );
			echo( "Select a game to join" );
		}
		else
		{
			// Multiple active games.  TODO: make this form do something, if multiple game support is required.
			echo( "<h4>Multple Active Games</h4>You're participating in more than one game right now.  Which game would you like to join?<p/>\n" );
			echo( "<form method=\"post\" action=\"log.php\">\n" );
			echo( "<select name=\"gameChoice\">\n" );
			echo( "<option value=\"" . $row['gameinstance'] . "\">Game #" . $row['gameinstance'] . " (" . $row['gn'] . ")</option>\n" );
			echo( "</select>\n<input type=\"hidden\" name=\"validated\" value=\"1\"/>\n" );
			echo( "<input type=\"hidden\" name=\"uuid\" value=\"" . $uuid . "\"/>\n" );
			echo( "<input type=\"hidden\" name=\"mode\" value=\"gameChoice\"/>\n" );
			echo( "<input type=\"Submit\" value=\"Join game\"></form>\n" );
		}	
	}

	// Username/password were correct.  Set up some session variables and redirect to the homepage.
	echo( "<h1>Login successful</h1>\nSending you to your user page.\n" );
	$_SESSION['uuid'] = $uuid;
	$_SESSION['uname'] = $uname;
	$_SESSION['gameinstance'] = $gameinstance;

	// Keep track of logins for assessment, data analysis, etc., in case the data turn out to be useful.
	$stmt = $dbh->prepare( "INSERT INTO logins(uid) VALUES(?)" );
	$stmt->bindParam( 1, $uuid );
	$stmt->execute( );
	
	// Redirect to the user frontpage after a short pause (or instantaneously in WebKit browsers...) 
	echo( "<script language=\"javascript\">var t = setTimeout( redirect( ), 4000 ); </script>\n" );
?>
</body>
</html>
