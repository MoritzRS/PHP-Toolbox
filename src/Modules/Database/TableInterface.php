<?php

namespace Modules\Database;

class TableInterface {
    protected static $databasePath;
    protected static $tableName;
    protected static $defaultProperties;
    protected $properties;
    protected $rowID = false;

    /**
     * Creates new Entry Object
     * @param array $properties
     * @param int|false $rowID
     */
    public function __construct(array $properties, $rowID = false) {
        $this->properties = [];
        $this->rowID = $rowID;
        $this->set($properties);
    }

    /**
     * Find Single Table Entry by given arguments
     * @param array $where WHERE Arguments
     * @param array $order ORDER Arguments
     * @param int|false $limit LIMIT
     * @param int|false $offset OFFSET
     * @return static|false
     */
    public static function find(array $where = [], array $order = [], $offset = false) {
        $db = new Database(static::$databasePath);
        $db->beginTransaction();
        $queryParts = static::queryBuilder($where, $order, 1, $offset);
        $query = "SELECT *, rowid FROM "
            . static::$tableName
            . $queryParts["where"]
            . $queryParts["order"]
            . $queryParts["limit"]
            . $queryParts["offset"];
        $result = $db->fetchSingle($query, $queryParts["parameters"]);
        $db->rollBack();
        return $result
            ? new static($result, $result["rowid"])
            : false;
    }

    /**
     * Find Table Entries by given arguments
     * @param array $where WHERE Arguments
     * @param array $order ORDER Arguments
     * @param int|false $limit LIMIT
     * @param int|false $offset OFFSET
     * @return array|false
     */
    public static function findAll(array $where = [], array $order = [], $limit = false, $offset = false) {
        return static::filter(array_keys(static::$defaultProperties), $where, $order, $limit, $offset);
    }

    /**
     * Query multiple entries but only use the given properties to reduce database load
     * @param array $properties Properties to use
     * @param array $where WHERE Arguments
     * @param array $order ORDER Arguments
     * @param int|false $limit LIMIT
     * @param int|false $offset OFFSET
     * @return array|false
     */
    public static function filter(array $properties, array $where = [], array $order = [], $limit = false, $offset = false) {
        $db = new Database(static::$databasePath);
        $db->beginTransaction();
        $queryParts = static::queryBuilder($where, $order, $limit, $offset);
        $propString = empty($properties) ? "" : ", " . implode(", ", array_filter($properties, function ($e) {
            return isset(static::$defaultProperties[$e]);
        }));
        $query = "SELECT "
            . "rowid"
            . $propString
            . " FROM "
            . static::$tableName
            . $queryParts["where"]
            . $queryParts["order"]
            . $queryParts["limit"]
            . $queryParts["offset"];
        $result = $db->fetch($query, $queryParts["parameters"]);
        $db->rollBack();
        return $result
            ? array_map(function ($e) {
                return new static($e, $e["rowid"]);
            }, $result)
            : false;
    }

    /**
     * Checks if Table has matching entries
     * @param array $where WHERE Arguments
     * @return bool
     */
    public static function has(array $where = []) {
        $db = new Database(static::$databasePath);
        $db->beginTransaction();
        $queryParts = static::queryBuilder($where);
        $query = "SELECT rowid FROM "
            . static::$tableName
            . $queryParts["where"];
        $success = $db->fetchSingle($query, $queryParts["parameters"]);
        $db->rollBack();
        return !!$success;
    }

    /**
     * Builds query parts from given arguments
     * @param array $where WHERE Arguments
     * @param array $order ORDER Arguments
     * @param int|false $limit LIMIT
     * @param int|false $offset OFFSET
     * @return array Query Parts as associative array
     */
    private static function queryBuilder(array $where = [], array $order = [], $limit = false, $offset = false) {
        $orderString = empty($order) ? "" : " ORDER BY " . implode(", ", $order);
        $limitString = $limit === false ? "" : " LIMIT {$limit}";
        $offsetString = $offset === false ? "" : " OFFSET {$offset}";
        $params = [];
        $whereString = "";
        if (!empty($where)) {
            $whereArray = [];
            foreach ($where as $key => $value) {
                if (is_int($key)) $whereArray[] = "($value)";
                else {
                    $whereArray[] = "({$key} = :{$key})";
                    $params[":{$key}"] = $value;
                }
            }
            $whereString = " WHERE " . implode(" AND ", $whereArray);
        }
        return [
            "where" => $whereString,
            "order" => $orderString,
            "limit" => $limitString,
            "offset" => $offsetString,
            "parameters" => $params
        ];
    }


    /**
     * Sets the given property values
     * @param array $properties Properties (names keys)
     */
    public function set(array $properties) {
        foreach ($properties as $key => $value) {
            if (!isset(static::$defaultProperties[$key])) continue;
            $this->properties[$key] = $value;
        }
    }

    /**
     * Gets the given property values
     * @param string|array $properties
     * @return mixed|array|null
     */
    public function get($properties) {
        if (is_string($properties)) {
            return $this->properties[$properties] ?? null;
        }
        return array_combine($properties, array_map(function ($e) {
            return $this->properties[$e] ?? null;
        }, $properties));
    }

    /**
     * Persist Data in Database
     * @return bool Success of Operation
     */
    public function save() {
        // update
        if ($this->rowID !== false) {
            $updateString = implode(", ", array_map(function ($e) {
                return "{$e} = :{$e}";
            }, array_keys($this->properties)));
            $query = "UPDATE " . static::$tableName . " SET {$updateString} WHERE rowid = :rowid";
            $parameters = array_combine(array_map(function ($e) {
                return ":{$e}";
            }, array_keys($this->properties)), array_values($this->properties));
            $parameters[":rowid"] = $this->rowID;
            $db = new Database(static::$databasePath);
            $db->beginTransaction();
            $success = $db->execute($query, $parameters);
            if (!$success) {
                $db->rollBack();
                return false;
            }
            $db->commit();
            return true;
        }

        // insert
        $propKeys = array_keys($this->properties);
        $paramKeys = array_map(function ($e) {
            return ":{$e}";
        }, $propKeys);
        $propKeyString = implode(", ", $propKeys);
        $paramKeyString = implode(", ", $paramKeys);
        $query = "INSERT INTO " . static::$tableName . "({$propKeyString}) VALUES({$paramKeyString});";
        $parameters = array_combine($paramKeys, array_values($this->properties));
        $db = new Database(static::$databasePath);
        $db->beginTransaction();
        $success = $db->execute($query, $parameters);
        if (!$success) {
            $db->rollBack();
            return false;
        }
        $rowidQuery = "SELECT last_insert_rowid() as rid;";
        $rawRowID = $db->fetchSingle($rowidQuery)["rid"];
        $this->rowID = intval($rawRowID);
        $db->commit();
        return true;
    }

    /**
     * Deletes Entry From Table
     * @return bool Success of Operation
     */
    public function delete() {
        if ($this->rowID === false) return true;
        $query = "DELETE FROM " . static::$tableName . " WHERE rowid = :rid";
        $parameters = [":rid" => $this->rowID];
        $db = new Database(static::$databasePath);
        $db->beginTransaction();
        $success = $db->execute($query, $parameters);
        if ($success) $db->commit();
        else $db->rollBack();
        return !!$success;
    }
}
