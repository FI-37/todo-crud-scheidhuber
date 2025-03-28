<?php
header('Content-Type: application/json');

// LOG function in PHP
function write_log($action, $data) {
    $log = fopen('log.txt', 'a');
    $timestamp = date('Y-m-d H:i:s');
    fwrite($log, "$timestamp - $action: " . json_encode($data) . "\n");
    fclose($log);
}

write_log("init", "todo-api.php was called");

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Placeholder for reading TODO items
        break;
    case 'POST':
        // Placeholder for creating a new TODO
        break;
    case 'PUT':
        // Placeholder for updating a TODO
        break;
    case 'DELETE':
        // Placeholder for deleting a TODO
        break;
}
?>