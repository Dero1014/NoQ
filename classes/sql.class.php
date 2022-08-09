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
    public function InsertValuesStmt(string $types, string $command, array &...$vars)
    {
        echo "Insert started";
        // TODO : Create a statement
        $stmt = $this->query->prepare();

        // TODO: RUN THE STATEMENT
        $this->query->execute();

        echo "Values inserted\n";
        return true;
    }

    private function PrepStmt(string $command)
    {
        // TODO: INITILIZE THE STATEMENT AND PREPARE IT
        $stmt = mysqli_stmt_init($this->query);
    
        if (!mysqli_stmt_prepare($stmt, $command)) {
            die("$command");
            header("Location: index.php?error=stmtfail");
            exit();
        }
        echo "Statement prepared\n";
        return $stmt;
    }
}