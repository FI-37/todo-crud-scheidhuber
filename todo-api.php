<?php
header('Content-Type: application/json');

// LOG function in PHP
function write_log($action, $data) {
    $log = fopen('log.txt', 'a');
    $timestamp = date('Y-m-d H:i:s');
    fwrite($log, "$timestamp - $action: " . json_encode($data) . "\n");
    fclose($log);
}

// Read content of the file and decode JSON data to an array.
$todo_file = 'todo.json';
if (file_exists($todo_file)) {
    $todo_items = json_decode(
        file_get_contents($todo_file),
        true);
} else {
    $todos_items = [];
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        echo json_encode($todo_items);
        write_log("GET", $todo_items);
        break;
    case 'POST':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);
        // Create new todo item.
        $new_todo = ["id" => uniqid(), "title" => $data['title'], "completed" => false];
        // Add new item to our todo item list.
        $todo_items[] = $new_todo;
        // Write todo items to JSON file.
        file_put_contents($todo_file, json_encode($todo_items));
        // Return the new item.
        echo json_encode($new_todo);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        // Search for the given todo id and updated the completed field
        foreach ($todo_items as &$todo) {
            if ($todo['id'] == $data['id']) {
                $todo['completed'] = $data['completed'];
                break;
            }
        }
        // Get the changed item back to the client.
        file_put_contents($todo_file, json_encode($todo_items));
        echo json_encode($data);
        write_log("PUT", $data);
        break;
    case 'DELETE':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);
        // Filter Todo to delete from the list.
        $todo_items = array_values(
            array_filter($todo_items,
                function($todo) use ($data) {
                    return $todo['id'] !== $data['id'];
        }));
        // Write the Todos back to JSON file.
        file_put_contents('todo.json', json_encode($todo_items));
        // Tell the client the success of the operation.
        echo json_encode(['status' => 'success']);
        write_log("DELETE", $data);
        break;
}
?>