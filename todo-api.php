<?php
header('Content-Type: application/json');

$host = '127.0.0.1';
$db = 'todo_list';
$user = 'j23d';
$pass = 'beep';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log("PDOException: " . $e->getMessage() . " in "
              . $e->getFile() . " on line " . $e->getLine());
}

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
        $statement = $pdo->query("SELECT * FROM todo");
        $todo_items = $statement->fetchAll();
        echo json_encode($todo_items);
        write_log("GET", $todo_items);
        break;
    case 'POST':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);

        // Insert given data as new todo into database.
        $statement = $pdo->prepare(
            "INSERT INTO todo (title, completed) VALUES (:title, :completed)");
        $statement->execute(['title' => $data['title'], 'completed' => 0]);

        // Return success message.
        echo json_encode(['status' => 'success']);
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