<?php

/**
 * Live auction data API endpoint for Fantasy Collecting.
 *
 * Provides real-time auction data via JSON for client-side polling in the
 * live auction system. Returns current high bidder, last bid time, and
 * bid amount. Called periodically by JavaScript from marketplace.php.
 *
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 *
 * @version    0.2 (modernized)
 *
 * @since      2012-10-01 (original), 2025-09-10 (modernized)
 *
 * @license    MIT
 *
 * @param int $_GET['i'] The auction ID (auctions table primary key)
 */
if (session_id() == '') {
    session_start();
}

header('Content-type: application/json');

ob_start();
require 'db.php';
require 'functions.php';
ob_end_clean();

$requested = $_GET['i'];

// Set upt he JSON object containing our data.  Sample format: {"amt":120,"u":16,"t":128974918274, "metReserve":0 }
// amt = bid amount; u = user ID of high bidder; t: epoch timestamp of last bid; metReserve: was reserve met? 1/0
$JSON = '{"amt":' . getHighBidAmountForAuction($requested) . ', "u":"' . getHighBidderForAuction($requested) . '", "t":' . getLastBidTimeForAuction($requested) . ', "metReserve":' . didAuctionMeetReserve($requested) . '}';
echo $JSON;
