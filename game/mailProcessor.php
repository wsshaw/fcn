<?php     
	/**
	 * Message delivery processor for Fantasy Collecting mail system
	 * 
	 * Processes mail form submissions and delivers messages to intended recipients.
	 * Handles dual calling modes: AJAX requests from response forms in mail.php
	 * and standard POST requests from mailCompose.php. Generates appropriate
	 * output for standard POST requests.
	 *
	 * @package    FantasyCollecting
	 * @author     William Shaw <william.shaw@duke.edu>
	 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
	 * @version    0.2 (modernized)
	 * @since      2012-05-01 (original), 2025-09-10 (modernized)
	 * @license    MIT
	 * 
	 * @param string $_POST['string']  The message body content
	 * @param string $_POST['address'] The recipient username
	 */ 

	if(session_id() == '') {
        	session_start();
	}
        
        $gameinstance = $_SESSION['gameinstance'];
        $uname = $_SESSION['uname'];
        $uuid = $_SESSION['uuid'];
	$recipient = $_POST['address'];
	$message = strip_tags( $_POST['string'] );

        ob_start( );
		require 'functions.php';        
		require 'db.php';
	ob_end_clean( );

        logVisit( $uuid, basename( __FILE__ ) );
?>
<html>  
<head>  
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
        google.load( "jquery", "1" );
        google.load( "jqueryui", "1" );
</script>
<link rel="stylesheet" href="resources/fcn.css" type="text/css"/>
<link rel="stylesheet" type="text/css" href="resources/jquery-ui.css"/>
<script type="text/javascript">
	$(document).ready( function( ) {
		// mailCompose opens in a Shadowbox, so hook into it for dismiss button functionality
		var s = window.parent.Shadowbox;
		s.setDimensions( 150, 200, 150, 200, 0, 0, 0, true );
		$( "#dismiss" ).button( );
		$( "#dismiss" ).click( function( ) {
			window.parent.Shadowbox.close( );	
		} );
	} );
</script>
</head>
<body style="background-color:#fff">
<?php
	$recipientUid = getUserId( $recipient );

	$stmt = $dbh->prepare( "INSERT INTO msgs(uidf,uidt,gid,string,rr) VALUES( ?, ?, ?, ?, ? )" );
	$stmt->bindParam( 1, $uuid );
	$stmt->bindParam( 2, $recipientUid );
	$stmt->bindParam( 3, $gameinstance );
	$stmt->bindParam( 4, $message );
	$stmt->bindValue( 5, 0 );	// read receipt -- not implemented

	$stmt->execute( );

	// Notify the recipient
	$mailNotification = "<a href=\"" . $FCN_ROOT . "mail.php\">" . getUsername( $uuid ) . " sent you a message.</a>";
	createNotification( $recipientUid, $E_MESSAGE_RECEIVED, $mailNotification );
?>
<h1>Message delivered</h1>
<button id="dismiss">Okay</button>
</body>
</html>
