<?php
session_start();  // MUST be first thing in the file

echo '<h2>Session Debug</h2>';
echo '<pre>';
var_dump($_SESSION); // Shows all session data
echo '</pre>';
exit; // Stops the rest of the page from loading
?>