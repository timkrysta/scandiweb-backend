<?php

namespace Timkrysta;

use mysqli;
use mysqli_stmt;
use PDO;
use PDOException;
use PDOStatement;

/**
 * Generic class for handling database operations.
 * Uses MySQLi or PDO with PreparedStatements.
 */
class DB
{
    private mysqli|PDO $conn;
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    public function __construct()
    {
        $credentialsFile = $_SERVER['DOCUMENT_ROOT'] . '/web-developer-test-assignment/' . 'database/credentials.json';
        $credentials = json_decode(file_get_contents($credentialsFile), true);

        $this->host     = $credentials['host'];
        $this->username = $credentials['username'];
        $this->password = $credentials['password'];
        $this->database = $credentials['database'];

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
     * Get the database results.
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

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $resultSet = [];
            while ($row = $result->fetch_assoc()) {
                $resultSet[] = $row;
            }
            return $resultSet;
        }

        return null;
    }

    /**
     * Insert into the database.
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
     * Execute a query.
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

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
    }

    /**
     * 1. Prepare parameters to bind.
     * 2. Bind prameters to the SQL statement
     *
     * @param mysqli_stmt|PDOStatement|false $stmt
     * @param string $paramType (There are four possible types: i, d, s and b, which stand for integer, double, string and binary)
     * @param array  $paramArray
     * 
     * @return void
     */
    public function bindQueryParams(mysqli_stmt|PDOStatement|false $stmt, string $paramType, array $paramArray = []): void
    {
        $paramValueReference[] = &$paramType;
        for ($i = 0, $paramCount = count($paramArray); $i < $paramCount; $i++) {
            $paramValueReference[] = &$paramArray[$i];
        }
        call_user_func_array(array(
            $stmt,
            'bind_param'
        ), $paramValueReference);
    }

    /**
     * Get record count.
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
        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
        $stmt->store_result();
        $recordCount = $stmt->num_rows;

        return $recordCount;
    }

        
    /**
     * Get placeholder made of question marks like ?, ?, ? ...
     *
     * @param  int $count
     * @return string
     */
    public static function getPlaceholders(int $count): string
    {
        return implode(', ', array_fill(0, $count, '?'));
    }
}
