<?php

namespace Timkrysta;

use mysqli;
use PDO;
use PDOException;

/**
 * Generic DB class for handling DB operations.
 * Uses MySQLi and PreparedStatements.
 */
class DB
{
    private mysqli|PDO $conn;

    /**
     * PHP implicitly takes care of cleanup for default connection types.
     * So no need to worry about closing the connection.
     *
     * Keeping things simple and that works!
     */
    public function __construct(
        private string $host,
        private string $username,
        private string $password,
        private string $database
    ) {
        $this->conn = $this->getConnection();
    }

    /**
     * If connection object is needed, use this method to get access to it.
     * Otherwise, use the other methods for insert / update / etc.
     * 
     * @return mysqli
     */
    public function getConnection(): mysqli
    {
        $conn = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($conn->connect_errno) {
            trigger_error("Problem with connecting to the database.");
        }

        $conn->set_charset("utf8");
        return $conn;
    }

    /**
     * If you wish to use PDO, use this function to get a connection instance.
     * 
     * @return PDO
     */
    public function getPdoConnection(): PDO
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database}";
            $conn = new PDO($dsn, $this->username, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit("PDO Connect Error: " . $e->getMessage());
        }

        return $conn;
    }

    /**
     * To get database results.
     *
     * @param string $query
     * @param string $paramType
     * @param array  $paramArray
     * 
     * @return array|null
     */
    public function select(string $query, string $paramType = '', array $paramArray = []): ?array
    {
        $stmt = $this->conn->prepare($query);

        if (! empty($paramType) && ! empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $resultset = [];
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
            return $resultset;
        }

        return null;
    }

    /**
     * To insert.
     *
     * @param string $query
     * @param string $paramType
     * @param array $paramArray
     * 
     * @return int|null
     */
    public function insert(string $query, string $paramType, array $paramArray): ?int
    {
        $stmt = $this->conn->prepare($query);
        $this->bindQueryParams($stmt, $paramType, $paramArray);

        $stmt->execute();
        $insertId = $stmt->insert_id;
        return $insertId ?: null;
    }

    /**
     * To execute query.
     *
     * @param string $query
     * @param string $paramType
     * @param array $paramArray
     * 
     * @return void
     */
    public function execute(string $query, string $paramType = '', array $paramArray = []): void
    {
        $stmt = $this->conn->prepare($query);

        if (! empty($paramType) && ! empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
    }

    /**
     * 1. Prepare parameters to bind.
     * 2. Bind prameters to the SQL statement
     *
     * @param string $stmt
     * @param string $paramType
     * @param array $paramArray
     * 
     * @return void
     */
    public function bindQueryParams(string $stmt, string $paramType, array $paramArray = []): void
    {
        $paramValueReference[] = & $paramType;
        for ($i = 0; $i < count($paramArray); $i ++) {
            $paramValueReference[] = & $paramArray[$i];
        }
        call_user_func_array(array(
            $stmt,
            'bind_param'
        ), $paramValueReference);
    }

    /**
     * To get database results.
     *
     * @param string $query
     * @param string $paramType
     * @param array $paramArray
     * 
     * @return int
     */
    public function getRecordCount(string $query, string $paramType = '', array $paramArray = []): int
    {
        $stmt = $this->conn->prepare($query);
        if (! empty($paramType) && ! empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
        $stmt->store_result();
        $recordCount = $stmt->num_rows;

        return $recordCount;
    }
}
