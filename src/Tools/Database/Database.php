<?php

namespace Tools\Database;

use Exception;
use PDO;

class Database {

    /**
     * PDO Object
     */
    private PDO $pdo;

    /**
     * Latest PDO Exception
     */
    private Exception $exception;


    public function __construct(string $path) {
        $this->pdo = $this->createPDO($path);
    }

    /**
     * Creates new PDO
     * @param string $path Path to Database
     * @return PDO
     */
    private function createPDO(string $path) {
        $PDO = new PDO("sqlite:" . $path);
        $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $PDO->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_TO_STRING);
        return $PDO;
    }

    /**
     * Checks if PDO operations threw any exceptions
     * @return bool
     */
    public function hasException() {
        return isset($this->exception);
    }

    /**
     * Get the last thrown exception
     * @return Exception | false
     */
    public function getException() {
        return $this->exception ?? false;
    }

    /**
     * Closes the database connection
     */
    public function close() {
        unset($this->pdo);
    }

    /**
     * Starts a new transaction
     */
    public function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    /**
     * Commits transaction
     */
    public function commit() {
        $this->pdo->commit();
    }

    /**
     * Roll back all changes in transaction
     */
    public function rollBack() {
        $this->pdo->rollBack();
    }

    /**
     * Execute a query on the database
     * @param string $query SQL Query
     * @param array $parameters Query parameters
     * @return boolean Success of the operation
     */
    public function execute(string $query, array $parameters = []) {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($parameters);
            return true;
        } catch (Exception $e) {
            $this->exception = $e;
            return false;
        }
    }

    /**
     * Execute a query on the database and returns the result
     * @param string $query SQL Query
     * @param array $parameters Query parameters
     * @return array[] | false Success of the operation
     */
    public function fetch(string $query, array $parameters = []) {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($parameters);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (Exception $e) {
            $this->exception = $e;
            return false;
        }
    }

    /**
     * Execute a query on the database and returns the first result
     * @param string $query SQL Query
     * @param array $parameters Query parameters
     * @return array | false Success of the operation
     */
    public function fetchSingle(string $query, array $parameters = []) {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($parameters);
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if (!isset($data[0])) throw new Exception("Empty Results");
            return $data[0];
        } catch (Exception $e) {
            $this->exception = $e;
            return false;
        }
    }
}
