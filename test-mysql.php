<?php
try  {
    $mysqli = new mysqli("localhost", "j23d", "beep", "test");
    echo "Erfolgreich verbunden!";

} catch (Exception $exc) {
    die("Verbindung fehlgeschlagen: " . $exc);
}
?>
