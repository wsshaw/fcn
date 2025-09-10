<?php
	// Database connection - modernized with proper error handling and security

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
