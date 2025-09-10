<?php
/**
 * Session logout and cleanup for Fantasy Collecting.
 *
 * Destroys the user session and cleans up session data when users log out
 * of the Fantasy Collecting game. Provides secure session termination.
 *
 * @author     William Shaw <william.shaw@duke.edu>
 * @author     Katherine Jentleson <katherine.jentleson@duke.edu> (designer)
 *
 * @version    0.2 (modernized)
 *
 * @since      2012-08-01 (original), 2025-09-10 (modernized)
 *
 * @license    MIT
 */
session_start();
session_destroy();
?><html>
<head>
<script type="text/javascript">
        google.load( "jquery", "1" );
        google.load( "jqueryui", "1" );
</script>
<script type="text/javascript">
</script>       
<link rel="stylesheet" href="fcn.css" type="text/css"/>
</head>                 
<body>    
<h1>Fantasy Collecting Network: Logged Out</h1>
<form action="log.php" method="post">
Username: <input type="text" name="username" value=""/> 
<p/>
Password: <input type="password" name="password" value=""/> 
<input type="submit" value="Log In">
</form>
</body>
</html>
