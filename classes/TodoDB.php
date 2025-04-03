<?php

require_once('./logging.php');
require_once('./config.php');

/**
 * Todo list database object.
 *
 * Global variable with the object of our TodoDB class.
 */
$todoDB = new TodoDB();

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

    public function getTodos() {
        $statement = $this->connection->query("SELECT * FROM todo");
        $todo_items = $statement->fetchAll();
        return $todo_items;
    }

}

?>