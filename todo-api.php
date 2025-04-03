<?php
header('Content-Type: application/json');

require_once('./logging.php');
require_once('./classes/TodoDB.php');


switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $todo_items = $todoDB->getTodos();
        echo json_encode($todo_items);
        write_log("GET", $todo_items);
        break;
    case 'POST':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);

        if(!isset($data["title"])) {
            echo json_encode(['status' => 'error', 'message' => '"title" is missing']);
            break;
        }

        // Insert given data as new todo into database.
        $statement = $pdo->prepare(
            "INSERT INTO todo (title, completed) VALUES (:title, :completed)");
        $statement->execute(['title' => $data['title'], 'completed' => 0]);

        // Return success message.
        echo json_encode(['status' => 'success']);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        // Update todo item in the database.
        if(isset($data["completed"])) {
            $statement = $pdo->prepare(
                "UPDATE todo SET completed = :completed WHERE id = :id");
            $statement->execute(["id" => $data["id"], "completed" => $data["completed"]]);
        } else if (isset($data["title"])) {
            $statement = $pdo->prepare(
                "UPDATE todo SET title = :title WHERE id = :id");
            $statement->execute(["id" => $data["id"], "title" => $data["title"]]);
        }

        // Tell the client the success of the operation.
        echo json_encode(['status' => 'success']);
        write_log("PUT", $data);
        break;
    case 'DELETE':
        // Get data from the input stream.
        $data = json_decode(file_get_contents('php://input'), true);

        // Delete todo item from the database.
        $statement = $pdo->prepare("DELETE FROM todo WHERE id = :id");
        $statement->execute(["id" => $data["id"]]);

        // Tell the client the success of the operation.
        echo json_encode(['status' => 'success']);
        write_log("DELETE", $data);
        break;
}
?>