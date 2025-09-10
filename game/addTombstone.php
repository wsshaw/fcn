<?php
	/**
	 * Artwork tombstone creation form for Fantasy Collecting
	 * 
	 * Provides form interface allowing players to add tombstone information
	 * (artist name and lifespan) to artworks in their collection. Form data
	 * is processed by tombstoneProcessor.php to update artwork metadata.
	 *
	 * @package    FantasyCollecting
	 * @author     William Shaw <william.shaw@duke.edu>
	 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
	 * @version    0.2 (modernized)
	 * @since      2012-08-01 (original), 2025-09-10 (modernized)
	 * @license    MIT
	 * 
	 * @param int $_GET['w'] The artwork ID to edit (works table primary key)
	 */

	if(session_id() == '') {
        	session_start();
	}
        
	$gameinstance = $_SESSION['gameinstance'];
        $uname = $_SESSION['uname'];
        $uuid = $_SESSION['uuid'];
	$workId = $_GET['w'];

	// Import database header and functions...
        ob_start( );
		require 'db.php';
                require 'functions.php';
        ob_end_clean( );

	// Log player's visit to this page
        logVisit( $uuid, basename( __FILE__ ) );

?>
<html>
<head>
<script type="text/javascript" src="https://www.google.com/jsapi"></script><script type="text/javascript">
        google.load( "jquery", "1" );
        google.load( "jqueryui", "1" );
</script>
<link rel="stylesheet" type="text/css" href="resources/fcn.css"/>
<link rel="stylesheet" type="text/css" href="resources/jquery-ui.css"/>

<script>
	// Set up the form submission button
	$(document).ready( function( ) {
		$( "button#submit" ).button( );
		$( "button#submit" ).click( function( ) {
			$("#tombstoneForm").submit( );
			return false; // prevent reload on WebKit
		} );	
	} ); 
</script>
</head>
<body style="background-color:#fff">
<form id="tombstoneForm" action="tombstoneProcessor.php" method="post">
<h2>Tombstone Information Form</h2>
<img src="img.php?img=<?php echo $workId;?>" style="width:300;margin-left:auto;margin-right:auto;display:block;"/>
<input type="hidden" name="work" value="<?php echo $workId;?>"/>
<p/>
	<label for="artist">Artist Name:</label><br/>
	<input id="artist" name="artist"/>
	<p/>
	<label for="born">Born:</label><br/>
	<input id="born" name="born"/>	
	<p/>
        <label for="died">Died:</label> <br/>
        <input id="died" name="died"/> 
	<p/>
	<label for="wt">Work Title:</label> <br/>
        <input id="wt" name="wt" size="80"/> 
	<p/>
        <label for="wd">Work Date:</label> <br/>
        <input id="wd" name="wd"/> 
<p/>
<div style="float:right;clear:both;"><button id="submit">Submit</button></div>
</div>
</form>
</body>
</html>
