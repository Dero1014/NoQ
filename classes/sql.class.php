<?php
include 'errorInfo.class.php';

class SQL
{
    private mysqli $query;
    private ErrorInfo $error;

    public function __construct()
    {
        $this->error = new ErrorInfo();
        $this->query = $this->connect();
    }

    // Methods
    // CONNECT TO DB
    private function connect()
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
    public function setStmtValues(string $types, string $command, array $vars)
    {
        $stmt = $this->PrepStmt($command);

        mysqli_stmt_bind_param($stmt, $types, ...$vars);

        $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    // RETURNS THE FIRST ROW
    public function getStmtRow(string $command)
    {
        $stmt = $this->PrepStmt($command);
        $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($resultData);
    }

    public function setStmtCompanyTable(string $tableName)
    {
        $sql = "CREATE TABLE $tableName(
            sId INT NOT NULL auto_increment,
            sName VARCHAR(100) NOT NULL,
            numberOfUsers INT DEFAULT 0,
            avgTime INT DEFAULT 0,
            timeSum INT DEFAULT 0,
            PRIMARY KEY (sId)
            );";

        $stmt = $this->PrepStmt($sql);
        $this->error->tryStmtError(mysqli_stmt_execute($stmt), $stmt);
    }

    private function PrepStmt(string $command)
    {
        // TODO: INITILIZE THE STATEMENT AND PREPARE IT
        $stmt = mysqli_stmt_init($this->query);

        if (!mysqli_stmt_prepare($stmt, $command)) {
            die("Command failed : $command");
            header("Location: index.php?error=stmtfail");
            exit();
        }
        echo "Statement prepared\n";
        return $stmt;
    }

    private function StmtErrorHandler($result, $stmt)
    {
        if (!$result) {
            echo mysqli_stmt_error($stmt);
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
