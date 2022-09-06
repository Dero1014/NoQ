<?php
/**
 * @brief Acts as bridge between the database and rest of the php
 * code
 */
class SQL
{
    protected mysqli $query;
    protected ErrorInfo $error;

    protected $log = FALSE;

    /**
     * @brief Grabs a connection for mysqli $query
     */
    public function __construct($from = "nobody")
    {
        $this->Log("I have been called by class: '$from'\n");
        $this->query = $this->connect();
    }

    // Methods:
    //  Protected:

    /**
     * @brief Connects to database
     * 
     * @return mysqli
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

        $this->error = new ErrorInfo();

        return $conn;
    }

    protected function Log($string)
    {
        if ($this->log) {
            echo $string;
        }
    }

    //  Public:

    /**
     * @brief Takes a sql command to insert values into specific tables
     * @param string $types - types of values being inserted (s, i, d)
     * @param string $command - sql command
     * @param array $vars - the values being inserted 
     * @return bool true - if it passes the result is true
     */
    public function setStmtValues(string $types, string $command, array $vars)
    {
        $stmt = $this->PrepStmt($command);
        mysqli_stmt_bind_param($stmt, $types, ...$vars);
        $result = $this->error->tryStmtError($stmt->execute(), $stmt);

        return $result;
    }

    /**
     * @brief Removes rows from a table where a column matches the value
     * @param string $tableName - table that we are deleting from
     * @param string $tableData - the column we are looking at
     * @param $var - the data we are looking for in that column
     * @return bool - if it passed it returns true
     */
    public function removeStmtValuesFrom(string $tableName, string $tableData, $var)
    {
        $sql = "DELETE FROM $tableName WHERE $tableData = $var;";
        if (gettype($var) === "string")
            $sql = "DELETE FROM $tableName WHERE $tableData = '$var';";

        $stmt = $this->PrepStmt($sql);

        $result = $this->error->tryStmtError($stmt->execute(), $stmt);

        return $result;
    }

    /**
     * @brief Takes a sql command and returns the first row result
     *        can use statement variant if neccesery
     * @param string $command - the sql command we are searching for
     * @param string $types - (optional) types of values being inserted (s, i, d)
     * @param string $vars - (optional) the values being searched for 
     * @return array | bool - returns the first row or false if it failed to find
     */
    public function getStmtRow(string $command, string $types = "", array $vars = [])
    {

        $stmt = $this->PrepStmt($command);

        if ($types != "") {
            mysqli_stmt_bind_param($stmt, $types, ...$vars);
        }

        $this->error->tryStmtError($stmt->execute(), $stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if ($resultData !== false) {
            return mysqli_fetch_assoc($resultData);
        } else {
            return false;
        }
    }

    /**
     * @brief Takes a SQL command and returns all of the rows
     * @param string $command - sql command we are searching for
     * @return array | false - the returned array always is in format [row][column]
     */
    public function getStmtAll(string $command)
    {
        $stmt = $this->PrepStmt($command);

        $this->error->tryStmtError($stmt->execute(), $stmt);
        $resultData = mysqli_stmt_get_result($stmt);

        if ($resultData !== false) {
            return mysqli_fetch_all($resultData);
        } else {
            return false;
        }
    }

    /**
     * @brief Creates a table by offering a name for the table and
     * it's contents
     * @param string $tableName - name of the table we want to create
     * @param string $tableContents - the contents of the table including the parantheses '()'
     * @return bool - true if it passes
     */
    public function createTable(string $tableName, string $tableContents)
    {
        $sql = "CREATE TABLE " . $tableName . $tableContents;

        $stmt = $this->PrepStmt($sql);

        return $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    /**
     * @brief Deletes table  
     * @param string $tableName - name of the table we want to delete
     * @return bool - true if it passes 
     */
    public function dropTable(string $tableName)
    {
        $sql = "DROP TABLE " . $tableName;

        $stmt = $this->PrepStmt($sql);

        return $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    /**
     * @brief Updates the values in a table
     * @param string $command - the sql command responsible for updating the table
     * @return bool - true if it passes
     */
    public function updateTable(string $command)
    {
        $stmt = $this->PrepStmt($command);

        return $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    /**
     * @brief Check if a table exists or not
     * @param string $tableName
     * @return bool true
     */
    public function findTable(string $tableName)
    {
        if ($result = $this->query->query("SHOW TABLES LIKE '$tableName'")) {
            if ($result->num_rows == 1) {
                echo "Table $tableName exists\n";
                return true;
            }
        } else {
            echo "Table $tableName does not exist\n";
            return false;
        }
    }

    //  Private:

    /**
     * @brief Takes a sql command and turns it into a statement
     * @param string $command
     * @return mysqli_stmt - if it fails it stops the code from running further
     */
    private function PrepStmt(string $command)
    {
        $stmt = mysqli_stmt_init($this->query);

        if (!mysqli_stmt_prepare($stmt, $command)) {
            $str = mysqli_stmt_error($stmt);
            die("Command failed : $command \n Error : $str \n");
            header("Location: index.php?error=stmtfail");
            exit();
        }

        $this->Log("Statement prepared for: $command\n");

        return $stmt;
    }
}