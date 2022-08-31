<?php

/**
 * @brief Contains functions for registering a new user
 */
class Register extends SQL
{
    /**
     * @brief Calls SQL to get a connection
     */
    public function __construct()
    {
        parent::__construct();
    }

    // Methods:
    //  Public:

    /**
     * @brief Adds a user to the Users table because the inspector processed
     * the values no inspection is necessary
     * @param $uName - username
     * @param $uPass - password
     * @param $uEmail - email
     * @param $uCompany - company tag
     * @param $cName - company name
     * @param $cDesc -company description
     * 
     * @return void
     */
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

    /**
     * @brief Adds a company into table Companies and creates two tables
     * for the workers and for the company itself
     * 
     * @param $uName - username 
     * @param $cName - company name
     * @param $cDesc - company description
     * 
     * @return void
     */
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
        $sql = "INSERT INTO Companies (cName, xcName, cDesc, userId) 
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

        // Create worker table
        $tableName = "WORKERS_" . $xcName;
        $tableContents = "(
            wId INT NOT NULL auto_increment,
            wName VARCHAR(100) NOT NULL,
            wPass VARCHAR(100) NOT NULL,
            myUser INT DEFAULT NULL,
            avgTime INT DEFAULT 0,
            timeSum INT DEFAULT 0,
            numberOfUsers INT DEFAULT 0,
            FOREIGN KEY (myUser) REFERENCES Users(uId),
            PRIMARY KEY (wId)
            );";
        $result = $this->createTable($tableName, $tableContents);

        if ($result) {
            $this->Log("Table has been created \n");
        } else {
            die("error creating table");
        }
    }
}