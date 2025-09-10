<?php
/**
 * Auction bid processing handler for Fantasy Collecting.
 *
 * Processes auction bid submissions from marketplace.php forms. Handles both
 * regular bids and "Buy It Now" purchases. Output is displayed in a shadowbox
 * dialog on the marketplace page after bid submission.
 *
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 *
 * @version    0.2 (modernized)
 *
 * @since      2013-02-01 (original), 2025-09-10 (modernized)
 *
 * @license    MIT
 *
 * @param int    $_POST['aid']            The auction ID (auctions table primary key)
 * @param float  $_POST['amountOutright'] The "Buy It Now" amount for immediate purchase
 * @param float  $_POST['amount']         Regular bid amount (if not using Buy It Now)
 * @param string $_POST['mode']           Deprecated absentee bid mode (preserved for compatibility)
 */
if (session_id() == '') {
    session_start();
}
$gameinstance = $_SESSION['gameinstance'];
$uname = $_SESSION['uname'];
$uuid = $_SESSION['uuid'];

ob_start();
require 'db.php';
require 'functions.php';
ob_end_clean();

$auctionId = $_POST['aid'];
$buyNowAmount = $_POST['amountOutright'];
$bidAmt = $_POST['amount'];
$mode = $_POST['mode'];

// Logic: if bidAmt has any text in it, we want that value; if it's blank, this has to be an outright
// purchase (buy it now).
if (strlen(trim($bidAmt)) > 0) {
    // Do nothing; use $bidAmt as is
} else {
    // $bidAmt must not be set, so assign it the BIN value
    $bidAmt = $buyNowAmount;
}

logVisit($uuid, basename(__FILE__));
?>
<link rel="stylesheet" type="text/css" href="resources/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="resources/fcn.css"/>
<link rel="stylesheet" type="text/css" href="resources/shadowbox.css">
<?php
    /** Deprecated: absentee bidding for live auctions.
    if ( $mode === "absentee" ) {
        $query = $dbh->prepare( "INSERT INTO absentee_bids(uid,aid,amt) VALUES(?,?,?)" );
        $query->bindParam( 1, $uuid );
        $query->bindParam( 2, $auctionId );
        $query->bindParam( 3, $bidAmt );
        $query->execute( );
        echo( "You have placed an absentee bid of " . $CURRENCY_SYMBOL . $bidAmt . ".");
        exit;
    }
     */

    // Series of sanity checks: did the user enter a positive integer as his/her bid?
    if ((!is_numeric($bidAmt)) || ($bidAmt < 1)) {
        echo $bidAmt . " isn't an acceptable bid.  Please bid only positive integers.";
        // Is the auction still active?  And is the user bidding an acceptable minimum?  (Current high bid + 5 FCG)
    } elseif ((isAuctionStillActiveFixedEnd($auctionId)) && ($bidAmt < (getHighBidAmountForAuction($auctionId) + 5))) {
        echo 'Please bid at least ' . $CURRENCY_SYMBOL . (getHighBidAmountForAuction($auctionId) + 5) . '.';
        echo '<p/><button id="dismiss" class="ui-widget ui-button ui-state-default ui-corner-all ui-button-text-only" style="width:100px;height:40px;" onClick="window.parent.Shadowbox.close( );">Okay</button>';
        // Is the attempted bid less than the minimum bid for the auction?
    } elseif ((isAuctionStillActiveFixedEnd($auctionId)) && ($bidAmt < getMinimumBidForAuction($auctionId))) {
        echo 'Sorry, but the minimum bid for this auction is ' . getMinimumBidForAuction($auctionId) . '.';
        echo '<p/><button id="dismiss" class="ui-widget ui-button ui-state-default ui-corner-all ui-button-text-only" style="width:100px;height:40px;" onClick="window.parent.Shadowbox.close( );">Okay</button>';
        // Does the user have enough money to place this bid?  If so, place it.
    } elseif ((isAuctionStillActiveFixedEnd($auctionId)) && ($bidAmt <= getPoints($uuid))) {
        placeBidFixedEnd($auctionId, $uuid, $bidAmt, $gameinstance);
        echo 'You have bid ' . $CURRENCY_SYMBOL . $bidAmt . ' on this work.';
        echo '<p/><button id="dismiss" class="ui-widget ui-button ui-state-default ui-corner-all ui-button-text-only" style="width:100px;height:40px;" onClick="window.parent.Shadowbox.close( );">Okay</button>';
        // Logically, this should come first so we don't call the function 3 times in a row for other checks... FIXME
    } elseif (!isAuctionStillActiveFixedEnd($auctionId)) {
        echo 'The auction has already ended!';
        echo '<p/><button id="dismiss" class="ui-widget ui-button ui-state-default ui-corner-all ui-button-text-only" style="width:100px;height:40px;" onClick="window.parent.Shadowbox.close( );">Okay</button>';
    } else {
        // Almost certainly due to a lack of points -- "place bid" button disappears when the auction ends.
        // Still, we should be a little more nuanced with the error state here.
        echo "You don't have " . $CURRENCY_SYMBOL . $bidAmt . '!';
        echo '<p/><button id="dismiss" class="ui-widget ui-button ui-state-default ui-corner-all ui-button-text-only" style="width:100px;height:40px;" onClick="window.parent.Shadowbox.close( );">Okay</button>';
    }
?>
