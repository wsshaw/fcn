<?php

/**
 * img.php: Display an image in the game, either at full size or as a thumbnail.
 * The reason this script exists at all is that it's useful for masking filenames
 * that might reveal information players are supposed to figure out on their own
 * (artist, work title, date, etc.).
 *
 * @author William Shaw <william.shaw@duke.edu>
 * @author Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 *
 * @version 0.2 (modernized)
 *
 * @since 2012-05 (original), 2025-09-10 (modernized)
 *
 * @license MIT
 *
 * @param int    $img  (via GET): ID of the image file (primary key of the works table)
 * @param string $mode (via GET): Either "full" or "thumb": if the former, just display the full size image; if
 *                     the latter, get the thumbnail image, which we expect to find in $FCN_IMAGES_PATH/thumbs/
 */
$img = $_GET['img'];
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'full';

ob_start();
require 'functions.php';
require 'db.php';
ob_end_clean();

$name = getImageURLFromId($img);
$fileNameWithPath = $_SERVER['DOCUMENT_ROOT'] . $FCN_IMAGES_PATH . ($mode === 'thumb' ? 'thumbs/' : '') . $name;

// The reson there's a call to session_write_close() here is that it fixes a weird, obscure
// problem with file I/O that used to cause Apache to hang forever if img.php were interrupted
// while loading an image.
session_write_close();

$fp = fopen($fileNameWithPath, 'rb');

header('Content-Type: image/jpeg');
header('Content-Length: ' . filesize($fileNameWithPath));

fpassthru($fp);
exit;
