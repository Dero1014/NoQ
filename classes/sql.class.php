<?php

class SqlCommands
{
    //private $command = "";
    private $query;

    public function __construct($conn)
    {
        $this->query = $conn;
    }

    // Methods

    // SET VALUES INTO A TABLE
    public function setStmtValues(string $types, string $command, array $vars)
    {
        $result = FALSE;

        // TODO : Create a statement
        $stmt = $this->PrepStmt($command);

        // TODO: RUN THE STATEMENT
        mysqli_stmt_bind_param($stmt, $types, ...$vars);

        $result = $this->StmtErrorHandler(mysqli_stmt_execute($stmt), $stmt);
        
        // RETURN RESULT
        return $result;
    }

    // RETURNS THE FIRST ROW
    public function getStmtRow(string $command)
    {
        $stmt = $this->PrepStmt($command);
        $result = $this->StmtErrorHandler(mysqli_stmt_execute($stmt), $stmt);

        if (!$result) {
            return $result;
        }else {
            $resultData = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($resultData);
        }
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
        $result = $this->StmtErrorHandler(mysqli_stmt_execute($stmt), $stmt);

        if (!$result) {
            return $result;
        }else {
            return $result;
        }
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
        }else
        {
            return TRUE;
        }
    }
}