<?php

class Inspector extends SQL
{
    public function __construct()
    {
        parent::__construct("inspector");
    }

    // Class ready functions (Functions that are used if the conditions to use a class are set) : 

    // Register user inspection
    /**
     * @brief Checks if the data that is being provided is suficient for registering a user
     * @param string $uName
     * @param string $uPass
     * @param string $uEmail
     * @param int $uCompany
     * @param string $cName
     * @param string $cDesc
     * 
     * @return bool
     */
    public function registerUserReady($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)
    {
        if ($uCompany === 1) {
            $words = array($uName, $uPass, $uEmail, $cName, $cDesc);
        } else {
            $words = array($uName, $uPass, $uEmail);
        }

        // Set table company name
        $xcName = str_replace(' ', '', $cName);
        $cDbName = "COMPANY_" . $xcName;

        $result =  $this->error->onRegisterError($this->areEmpty($words), 'empty');
        $result = $this->error->onRegisterError($this->areInvalid($words), 'invalid');
        $result = $this->error->onRegisterError($this->isNotEmail($uEmail), 'invalidMail');
        $result = $this->error->onRegisterError($this->alreadyExists($uName, 'uName', 'Users'), 'userExists');
        $result = $this->error->onRegisterError($this->alreadyExists($uEmail, 'uEmail', 'Users'), 'mailExists');
        if ($uCompany == 1) $result = $this->error->onRegisterError($this->tableExists($cDbName), 'companyExists');

        return $result;
    }

    // Login user inspection
    /**
     * @brief Checks if the data that is being provided is suficient for login a user
     * @param string $uName
     * @param string $uPass
     * 
     * @return bool
     */
    public function loginUserReady($uName, $uPass)
    {
        $words = array($uName, $uPass);
        $result =  $this->error->onLoginError($this->areEmpty($words), 'empty');
        $result = $this->error->onLoginError($this->areInvalid($words), 'invalid');
        $result = $this->error->onLoginError(!$this->alreadyExists($uName, 'uName', 'Users'), 'userNotExist');

        return $result;
    }

    // Service inspection
    /**
     * @brief Checks if the data that is being provided is suficient for a service to be added in a company
     * @param string $uName
     * @param string $uPass
     * 
     * @return bool
     */
    public function serviceReady($sName, $cTableName)
    {
        $words = array($sName);
        $result =  $this->error->onServiceError($this->areEmpty($words), 'empty');
        $result = $this->error->onServiceError($this->areInvalid($words), 'invalid');
        $result = $this->error->onServiceError($this->alreadyExists($sName, 'sName', $cTableName), 'serviceExists');

        return $result;
    }

    // Service DELETE QUEUE inspection
    /**
     * @brief Checks if the data that is being provided is suficient for a service QUEUE to be deleted from a company
     * @param string $qTableName
     * 
     * @return bool
     */
    public function serviceDeletionReady($qTableName)
    {
        $result =  $this->tableExists($qTableName);

        return $result;
    }

    // Worker inspection
    /**
     * @brief Checks if the data that is being provided is suficient for a worker to be added in a company
     * @param string $wName
     * 
     * @return bool
     */
    public function workerReady($wName, $wcName)
    {
        $words = array($wName);
        $result =  $this->error->onWorkerError($this->areEmpty($words), 'empty');
        $result = $this->error->onWorkerError($this->areInvalid($words), 'invalid');
        $result = $this->error->onWorkerError($this->alreadyExists($wName, 'wName', $wcName), 'workerExists');

        return $result;
    }

    // Queue inspection
    /**
     * @brief Checks if the data that is being provided is suficient for a queue to be started
     * @param string $wName
     * 
     * @return bool
     */
    public function queueReady($sName, $cName, $uId)
    {
        $xcName = str_replace(' ', '', $cName);
        $cDbName = "COMPANY_" . $xcName;
        $result = $this->error->onQueueError(!$this->alreadyExists($sName, 'sName', $cDbName), 'fail');
        $result = $this->error->onQueueError(!$this->alreadyExists($uId, 'uId', "Users"), 'fail2');

        return $result;
    }

    // Worker inspection
    /**
     * @brief Checks if the data that is being provided is suficient for a worker loged in
     * @param string $wName
     * 
     * @return bool
     */
    public function workerLoginReady($wComp, $wPass, $cn, $p)
    {
        $words = array($wComp, $wPass);
        echo "Company name $wComp \n";
        $wTableName =  'WORKERS_' . str_replace(' ', '', $wComp);
        echo "Table name" .$wTableName;
        $result = $this->error->onWorkerLoginError($this->areEmpty($words), 'empty', $cn, $p);
        $result = $this->error->onWorkerLoginError($this->areInvalid($words), 'invalid', $cn, $p);
        $result = $this->error->onWorkerLoginError(!$this->tableExists($wTableName), 'companyNonExistent', $cn, $p);

        return $result;
    }

    // Inspectors (general inspection checkers)

    // Empty inspector
    /**
     * @brief Checks if the data is empty
     * @param array $words
     * 
     * @return bool
     */
    private function areEmpty(array $words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (empty($words[$i])) {
                echo "Empty at " . $i . "\n";
                return true;
            }
        }
        echo "Inputs aren't empty\n";
        return false;
    }

    // Invalid input inspector
    /**
     * @brief Checks if the data isn't considered invalid
     * @param array $words
     * 
     * @return bool
     */
    private function areInvalid($words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (preg_match('/[\^£$%&*()}{#~?><>|=_+¬-]/', $words[$i])) {
                echo "Word: " . $words[$i] . " is empty\n";
                return true;
            }
        }
        echo "Array isn't invalid\n";
        return false;
    }

    // Email inspector
    /**
     * @brief Checks if the data provided is of a type email
     * @param string $words
     * 
     * @return bool
     */
    private function isNotEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Email is invalid $email\n";
            return true;
            //header("Location: ../sites/signup.site.php?signup=invalidemail");
        }
        echo "Email is valid \n";
        return false;
    }

    // Existing data inspector
    /**
     * @brief Checks if the data provided already exists in the database
     * @param $string
     * @param string $tData
     * @param string $table
     * 
     * @return bool
     */
    private function alreadyExists($var, $tData, $table)
    {
        $sql = "SELECT * FROM $table WHERE $tData = $var;";

        if (gettype($var) === "string")
            $sql = "SELECT * FROM $table WHERE $tData = '$var';";


        $row = $this->getStmtRow($sql);

        if ($row[$tData] == $var) {
            echo "Data [$tData] for $var exists! \n";
            return true;
        } else {
            echo "Data [$tData] for $var doesn't exists!\n";
            return false;
        }
    }


    // Existing table inspector
    /**
     * @brief Checks if a table already exists
     * @param string $cName
     * 
     * @return bool
     */
    private function tableExists($tableName)
    {
        if ($result = $this->query->query("SHOW TABLES LIKE '" . $tableName . "'")) {
            if ($result->num_rows == 1) {
                echo "Table $tableName exists\n";
                return true;
            }
        } else {
            echo "Table $tableName does not exist\n";
            return false;
        }
    }

    // Check if an interger exists
    private function alreadyExistsInt($conn, $int, $dbData, $db)
    {
        $sql = "SELECT * FROM $db WHERE $dbData = ?;";
        $stmt = startPrepStmt($conn, $sql);

        mysqli_stmt_bind_param($stmt, "i", $int);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($resultData);
        if ($row[$dbData] == $int) {
            echo "It exists\n";
            return true;
        } else {
            echo "It doesn't exist\n";
            return false;
        }

        mysqli_stmt_close($stmt);
    }
}
