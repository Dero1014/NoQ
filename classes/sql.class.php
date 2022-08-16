<?php
// SQL class handles all sql commands towards mysql
class SQL
{
    protected mysqli $query;
    protected $error;

    public function __construct($from = "nobody")
    {
        echo "I have been called by class: '$from'\n";
        $this->error = new ErrorInfo();
        $this->query = $this->connect();
    }

    // Methods:

    // CONNECT TO DB
    /**
     * @brief Connects to database
     * 
     * @return |connection
     */
    protected function connect()
    {
        $servername = "localhost";
        $username = "root";
        $password = "Ujaxcm+4%psPjyBr";
        $dbname = "noQdb";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        return $conn;
    }

    // SET VALUES INTO A TABLE
    /**
     * @brief Takes a specific command to add values via statements and runs it
     * @param string $types
     * @param string $command
     * @param array $vars
     * @return bool true
     */
    public function setStmtValues(string $types, string $command, array $vars)
    {
        $stmt = $this->PrepStmt($command);

        mysqli_stmt_bind_param($stmt, $types, ...$vars);
        $result = $this->error->tryStmtError($stmt->execute(), $stmt);

        return $result;
    }

    // GET VALUE FROM FIRST ROW
    /**
     * @brief Takes a specific command for select via statements, runs it
     *         and returns the first row
     * @param string $command
     * @return array | false
     */
    public function getStmtRow(string $command)
    {
        $stmt = $this->PrepStmt($command);

        $this->error->tryStmtError($stmt->execute(), $stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if ($resultData !== false) {
            return mysqli_fetch_assoc($resultData);
        }else {
            return false;
        }
    }

    // GET ALL OF THE VALUES
    /**
     * @brief Takes a specific command for select via statements, runs it
     *         and returns all results
     * @param string $command
     * @return array | false
     */
    public function getStmtAll(string $command)
    {
        $stmt = $this->PrepStmt($command);

        $this->error->tryStmtError($stmt->execute(), $stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if ($resultData !== false) {
            return mysqli_fetch_all($resultData);
        }else {
            return false;
        }
    }


    // CREATE A TABLE
    /**
     * @brief Takes a table name and its contents and creates it 
     * @param string $tableName
     * @param string $tableContents
     * @return bool true
     */
    public function createTable(string $tableName, string $tableContents)
    {
        $sql = "CREATE TABLE ". $tableName . $tableContents;

        $stmt = $this->PrepStmt($sql);
        return $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    // DROP A TABLE
    /**
     * @brief Takes a table name and deletes it 
     * @param string $tableName
     * @return bool true
     */
    public function dropTable(string $tableName)
    {
        $sql = "DROP TABLE ". $tableName;

        $stmt = $this->PrepStmt($sql);
        return $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }


    public function updateTable()
    {
    }

    // PREPARES A STATEMENT
    /**
     * @brief Takes a command and turns it into a statement
     * @param string $command
     * @return mysqli_stmt
     */
    private function PrepStmt(string $command)
    {
        $stmt = mysqli_stmt_init($this->query);

        if (!mysqli_stmt_prepare($stmt, $command)) {
            die("Command failed : $command");
            header("Location: index.php?error=stmtfail");
            exit();
        }

        echo "Statement prepared for: $command\n";
        return $stmt;
    }
}

/*
(
            sId INT NOT NULL auto_increment,
            sName VARCHAR(100) NOT NULL,
            numberOfUsers INT DEFAULT 0,
            avgTime INT DEFAULT 0,
            timeSum INT DEFAULT 0,
            PRIMARY KEY (sId)
            );
*/