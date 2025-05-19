<?php
require_once('Models/Database.php');

$dbContext = new Database();

try {
    // Logga ut användaren med PHP-Auth biblioteket
    $dbContext->getUsersDatabase()->getAuth()->logOut();
    
    // Rensa alla sessionsvariabler
    $_SESSION = array();
    
    // Förstör sessionen
    if (session_id() != "" || isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    
    // Omdirigera till startsidan
    header('Location: /');
    exit;
} catch (Exception $e) {
    // Logga felet
    error_log("Logout error: " . $e->getMessage());
    
    // Omdirigera till startsidan även vid fel
    header('Location: /');
    exit;
}
?>
