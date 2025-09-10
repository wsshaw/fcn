<?php
	/**
	 * Trade activity data generator for Fantasy Collecting dashboard
	 * 
	 * Generates JSON data describing all trade activity within the game for
	 * visualization purposes. Used to create chordal graphs displayed on the
	 * user dashboard (home.php) showing trading relationships between players.
	 *
	 * @package    FantasyCollecting
	 * @author     William Shaw <william.shaw@duke.edu>
	 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
	 * @version    0.2 (modernized)
	 * @since      2013-01-01 (original), 2025-09-10 (modernized)
	 * @license    MIT
	 */
        ob_start( );                
		require '../functions.php';        
		require '../db.php';
	ob_end_clean( );
?>

<?php
	header("Content-Type: application/json");
	// Trades JSON 

	$json = "[";
	
	$query = $dbh->prepare( "SELECT id,name,points FROM collectors WHERE id > 0" );
	$query->execute( );

	while( $row = $query->fetch( ) ) {

		$json .= "{\"name\":\"" . $row['name'] . "\", \"size\":" . $row['points'] . ",\"trades\":[";
		$leId = $row['id'];

		$subquery = $dbh->prepare( "SELECT origin,accepted,destination FROM trades WHERE origin=? AND accepted=1" );
		$subquery->bindParam( 1, $leId );
		$subquery->execute( );

		while( $subRow = $subquery->fetch( ) ) {
			$json .= "\"" . getUsername( $subRow['destination'] ) . "\",";
		}

		if ( $json[strlen($json)-1] != "[" ) {
			$json = substr($json,0,-1);
		}

		$json .= "]},\n";

	}		

	$json = substr( $json, 0, -2 );
	$json .= "]";

	echo $json;
?>
