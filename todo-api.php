<?php
header('Content-Type: application/json');

// LOG function in PHP
function write_log($action, $data) {
    $log = fopen('log.txt', 'a');
    $timestamp = date('Y-m-d H:i:s');
    fwrite($log, "$timestamp - $action: " . json_encode($data) . "\n");
    fclose($log);
}

$todo_items = [
    ["id" => "someUniqueId", "title" => "Erste Aufgabe"]
];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($todo_items);
        write_log("GET", $todo_items);
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