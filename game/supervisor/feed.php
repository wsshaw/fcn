<?php
/**
 * feed.php: utility script to display an abbreviated newsfeed on the supervisor page.
 *
 * @package FantasyCollecting
 * @author William Shaw <william.shaw@duke.edu>
 * @author Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 * @version 0.2 (modernized)
 * @since 2006 (original), 2025-09-10 (modernized)
 * @license MIT
 */
        ob_start( );
		require '../db.php';
                require '../functions.php';
        ob_end_clean( );

?>
<html>
<head>                                  
<link rel="stylesheet" type="text/css" href="../resources/fcn.css"/>
</head>
                <?php

                        $query = $dbh->prepare( "SELECT * FROM events ORDER BY date DESC LIMIT 20" );
                        $query->execute( );
                        while( $row = $query->fetch( ) ) {
                                echo displayEvent( $row, $CONTEXT_EVENT_FEED );
                        }
                ?>
</html>
