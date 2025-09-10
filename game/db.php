<?php
/**
 * Database connection and initialization for Fantasy Collecting
 * 
 * Establishes secure PDO connection to MySQL database with proper error handling,
 * security options, and initializes the Game Genie (superuser) account reference.
 * 
 * @package    FantasyCollecting  
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (original design)
 * @version    0.2 (modernized)
 * @since      2012-08-01 (original), 2025-09-10 (modernized)
 * @license    MIT
 */

        $username = "";
        $password = "";
        
        try {
            // Set PDO options for security
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
            ];
            
            $dbh = new PDO('mysql:host=localhost;dbname=fcn;charset=utf8mb4', $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }

        // Get game genie (superuser) - using prepared statements
        $gg = $dbh->prepare("SELECT id FROM collectors ORDER BY id ASC LIMIT 1");
        $gg->execute();
        $gameGenie = -1;
        if ($row = $gg->fetch()) {
            $gameGenie = (int)$row['id'];
        }

?>
