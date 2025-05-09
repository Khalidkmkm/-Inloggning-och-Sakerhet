<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset(); // Tar bort alla session-variabler
session_destroy(); // Förstör sessionen
header("Location: index.php"); // Skicka användaren till startsidan
exit;
?> 