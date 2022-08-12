<?php

class Register extends SQL
{
    public function addUser($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)
    {
        $sql = "INSERT INTO Users (uName, uPassword, uEmail, uCompany) 
                VALUES (?, ?, ?, ?);";

        $hashedPwd = password_hash($uPass, PASSWORD_DEFAULT);

        // Add user to table Users
        $this->setStmtValues("sssi", $sql, array($uName, $hashedPwd, $uEmail, $uCompany));
       
        echo "Registration successfull \n";

        // If user has a company add it
        if ($uCompany === 1) {
            $this->addCompany($uName, $cName, $cDesc);            
        }
    }

    private function addCompany($uName, $cName, $cDesc)
    {
        // Set no space name
        $xcName = str_replace(' ', '', $cName);

        // Get user id
        $userId = 0;

        $sql = "SELECT * FROM Users WHERE uName = '$uName';";

        $row = $this->getStmtRow($sql);
        $userId = $row['uId'];

        // Insert company into table
        $sql = "INSERT INTO Companies (cName, xcName, cDecs, userId) 
        VALUES (?, ?, ?, ?);";
        $this->setStmtValues("sssi", $sql, array($cName, $xcName, $cDesc, $userId));

        // Create company table
        $tableName = "COMPANY_" . $xcName;
        $tableContents = "(
        sId INT NOT NULL auto_increment,
        sName VARCHAR(100) NOT NULL,
        numberOfUsers INT DEFAULT 0,
        avgTime INT DEFAULT 0,
        timeSum INT DEFAULT 0,
        PRIMARY KEY (sId)
        );";
        $result = $this->createTable($tableName, $tableContents);

        if ($result) {
            echo "Table has been created \n";
        } else {
            die("error creating table");
        }
    }

}

class Login
{
}
