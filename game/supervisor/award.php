<?php
/**
 * Player points award/penalty system for Fantasy Collecting administrators.
 *
 * Allows game administrators to arbitrarily award points to or penalize players
 * by adjusting their FCG (Fantasy Collecting Game currency) totals. Provides
 * administrative tools for game balance and player incentives.
 *
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 *
 * @version    0.2 (modernized)
 *
 * @since      2012-08-01 (original), 2025-09-10 (modernized)
 *
 * @license    MIT
 *
 * @param int    $_GET['collector'] The player ID to award/penalize
 * @param string $_GET['desc']      Description message for the award/penalty
 * @param int    $_GET['points']    Number of points to award (positive) or penalize (negative)
 */
ob_start();
require '../functions.php';
require '../db.php';
ob_end_clean();

$player = $_GET['collector'];
$message = $_GET['desc'];
$points = $_GET['points'];

createNotification($player, $E_HAZARD, $message);
adjustPoints($player, $points);

echo 'Divine intervention complete.';
?>

