<?php

require_once('./logging.php');
require_once('./config.php');


/**
 * Database handling for the todos in the FI37 demo project.
 *
 * All database functionality is defined here.
 *
 * @author  US-FI37 <post@fi37-coding.com>
 * @property object $connection PDO connection to the MariaDB
 * @property object $stmt Database statement handler object.
 * @since 1.0
 */
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

        /**
     * Return the current todo items.
     *
     * @return array $todo_items The current todo items.
     */
    public function getTodos() {
        $statement = $this->connection->query("SELECT * FROM todo");
        $todo_items = $statement->fetchAll();
        return $todo_items;
    }

    /**
     * Add a new todo item.
     *
     * @param string $title The title of the new todo.
     */
    public function addTodo($title) {
        $this->prepareExecuteStatement(
            "INSERT INTO todo (title, completed) VALUES (:title, :completed)",
            ['title' => $title, 'completed' => 0]
        );
    }

    /**
     * Set the completion state of a todo item.
     *
     * @param int $id The id of the todo.
     * @param int $completed The completed value.
     */
    public function setCompleted($id, $completed) {
        $statement = $this->prepareExecuteStatement(
            "UPDATE todo SET completed = :completed WHERE id = :id",
            ["id" => $id, "completed" => $completed]);
    }

    /**
     * Set the updated title of a todo item.
     *
     * @param int $id The id of the todo.
     * @param string $title The new title.
     */
    public function updateTodo($id, $title) {#
        $statement = $this->prepareExecuteStatement(
            "UPDATE todo SET title = :title WHERE id = :id",
            ["id" => $id, "title" => $title]);
    }

    /**
     * Delete a todo item.
     *
     * @param int $id The id of the todo to delete.
     */
    public function deleteTodo($id) {
        $statement = $this->prepareExecuteStatement(
            "DELETE FROM todo WHERE id = :id",
            ["id" => $id]);
    }

}

?>