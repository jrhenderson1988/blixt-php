<?php

namespace Blixt\Storage\SQLite;

use DateTimeInterface;
use PDO;

class Connection
{
    /**
     * The PDO object we're dealing with.
     *
     * @var \PDO
     */
    protected $pdo;

    /**
     * Connection constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        echo $path;
        $this->pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }

    /**
     * Destroy the PDO object upon destruction of the class.
     */
    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * Run a select statement and return the first row. Null is returned if the result array is empty or not an array.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return array|null
     */
    public function selectOne($query, $bindings = [])
    {
        $result = $this->select($query, $bindings);

        return array_shift($result);
    }

    /**
     * Run a select statement and return the results.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return array
     */
    public function select($query, $bindings = [])
    {
        $statement = $this->pdo->prepare($query);

        $statement->execute($this->prepareBindings($bindings));

        return $statement->fetchAll();
    }

    /**
     * Run an insert statement, returning the last inserted ID where appropriate.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int|bool
     */
    public function insert($query, $bindings = [])
    {
        if ($this->statement($query, $bindings)) {
            return $this->pdo->lastInsertId();
        }

        return false;
    }

    /**
     * Run an update statement, returning the number of affected rows.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement, returning the number of affected rows.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a statement, returning true or false, based upon whether the query succeeded or failed.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function statement($query, $bindings = [])
    {
        $statement = $this->pdo->prepare($query);

        return $statement->execute($this->prepareBindings($bindings));
    }

    /**
     * Run a statement, returning the number of rows affected by the query.
     *
     * @param string $query
     * @param array  $bindings
     *
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        $statement = $this->pdo->prepare($query);

        $statement->execute($this->prepareBindings($bindings));

        return $statement->rowCount();
    }

    /**
     * Prepare the bindings for a query.
     *
     * TODO - Ensure any value can be provided as a binding. Account for arrays etc. (Serialize?)
     *
     * @param array $bindings
     *
     * @return array
     */
    public function prepareBindings($bindings)
    {
        foreach ($bindings as $key => $value) {
            if (is_bool($value)) {
                $bindings[$key] = $value ? 1 : 0;
            } elseif ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->getTimestamp();
            }
        }

        return $bindings;
    }

    /**
     * Begin a transaction.
     *
     * @return bool
     */
    public function beginTransaction()
    {
        if ($this->pdo->inTransaction()) {
            return true;
        }

        return $this->pdo->beginTransaction();
    }

    /**
     * Roll back the current transaction.
     *
     * @return bool
     */
    public function rollBackTransaction()
    {
        if (!$this->pdo->inTransaction()) {
            return false;
        }

        return $this->pdo->rollBack();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool
     */
    public function commitTransaction()
    {
        if (!$this->pdo->inTransaction()) {
            return false;
        }

        return $this->pdo->commit();
    }
}