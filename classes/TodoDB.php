<?php

require_once('./logging.php');
require_once('./config.php');


class TodoDB {
    private $connection;
    private $stmt;

    /**
     * Contructructor of the TodoDB class.
     */
    public function __construct() {
        global $host, $db, $user, $pass;
        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$db;",
                $user,
                $pass
            );
            $this->connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            write_log("HINT", "connection established");
        } catch (Exception $e) {
            write_log("ERROR", $e->getMessage());
        }
    }

    /**
     * Prepare and execute the given sql statement.
     *
     * @param string $sql The sql statement.
     * @param array $params An array of the needed parameters.
     * @return object $stmt The excecuted statement.
     */
    private function prepareExecuteStatement($sql, $params = []) {
        try {
            write_log("SQL", $sql);
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function getTodos() {
        $statement = $this->connection->query("SELECT * FROM todo");
        $todo_items = $statement->fetchAll();
        return $todo_items;
    }

    public function addTodo($title) {
        $this->prepareExecuteStatement(
            "INSERT INTO todo (title, completed) VALUES (:title, :completed)",
            ['title' => $title, 'completed' => 0]
        );
    }

    public function setCompleted($id, $completed) {
        $statement = $this->prepareExecuteStatement(
            "UPDATE todo SET completed = :completed WHERE id = :id",
            ["id" => $id, "completed" => $completed]);
    }

    public function updateTodo($id, $title) {#
        $statement = $this->prepareExecuteStatement(
            "UPDATE todo SET title = :title WHERE id = :id",
            ["id" => $id, "title" => $title]);
    }

    public function deleteTodo($id) {
        $statement = $this->prepareExecuteStatement(
            "DELETE FROM todo WHERE id = :id",
            ["id" => $id]);
    }

}

?>