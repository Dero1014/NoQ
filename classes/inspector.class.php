<?php

/**
 * @brief Inspects incoming values to see if they are applicable
 * for current istuations
 */
class Inspector extends SQL
{
    /**
     * Calls the SQL constructor to get a connection
     */
    public function __construct()
    {
        parent::__construct("inspector");
    }

    // Methods:
    //  Public:
    //      User handling:

    /**
     * @brief Checks if the data that is being provided is valid for registering a user
     * @param string $uName - username
     * @param string $uPass - password
     * @param string $uEmail - email
     * @param int $uCompany - does user possess a company or not 
     * @param string $cName - company name
     * @param string $cDesc - company description
     * 
     * @return bool
     */
    public function registerUserReady($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc, $mobile = false)
    {
        if ($uCompany === 1)
            $words = array($uName, $uPass, $uEmail, $cName, $cDesc);
        else
            $words = array($uName, $uPass, $uEmail);

        // Set table company name
        $xcName = str_replace(' ', '', $cName);
        $cDbName = "COMPANY_" . $xcName;

        $result =  $this->error->onRegisterError($this->areEmpty($words), 'empty', $mobile);
        $result = $this->error->onRegisterError($this->areInvalid($words), 'invalid', $mobile);
        $result = $this->error->onRegisterError($this->isNotEmail($uEmail), 'invalidMail', $mobile);
        $result = $this->error->onRegisterError($this->alreadyExists($uName, 'uName', 'Users'), 'userExists', $mobile);
        $result = $this->error->onRegisterError($this->alreadyExists($uEmail, 'uEmail', 'Users'), 'mailExists', $mobile);

        if ($uCompany == 1) $result = $this->error->onRegisterError($this->findTable($cDbName), 'companyExists', $mobile);

        return $result;
    }

    /**
     * @brief Checks if the data that is being provided is valid for login of a user
     * it already checks if the user exists or not in the database
     * @param string $uName - username
     * @param string $uPass - password
     * @param bool $mobile - changes the type of data being returned by the 
     * errorinfo class
     * 
     * @return bool
     */
    public function loginUserReady($uName, $uPass, $mobile = false)
    {
        $words = array($uName, $uPass);

        $result =  $this->error->onLoginError($this->areEmpty($words), 'empty',  $mobile);
        $result = $this->error->onLoginError($this->areInvalid($words), 'invalid',  $mobile);
        $result = $this->error->onLoginError(!$this->alreadyExists($uName, 'uName', 'Users'), 'userNotExist',  $mobile);

        return $result;
    }

    //      Company handling:

    /**
     * @brief Checks if the data that is being provided is valid for a service to be 
     * added to the company
     * @param string $sName - service name that is being added
     * @param string $cTableName - the company the service is being added to
     * 
     * @return bool
     */
    public function serviceInsertReady($sName, $cTableName)
    {
        $words = array($sName);
        $result =  $this->error->onServiceError($this->areEmpty($words), 'empty');
        $result = $this->error->onServiceError($this->areInvalid($words), 'invalid');
        $result = $this->error->onServiceError($this->alreadyExists($sName, 'sName', $cTableName), 'serviceExists');

        return $result;
    }

    /**
     * @brief Checks if the data that is being provided is valid for a QUEUE of the service
     * to be deleted
     * @param string $qTableName - name of the queue table
     * 
     * @return bool
     */
    public function serviceQueueDeletionReady($qTableName)
    {
        $result =  $this->findTable($qTableName);

        return $result;
    }

    /**
     * @brief Checks if the data that is being provided is valid for a worker to be added 
     * in a company
     * @param string $wName - name of the worker
     * 
     * @return bool
     */
    public function workerInsertReady($wName, $wcName)
    {
        $words = array($wName);
        $result =  $this->error->onWorkerError($this->areEmpty($words), 'empty');
        $result = $this->error->onWorkerError($this->areInvalid($words), 'invalid');
        $result = $this->error->onWorkerError($this->alreadyExists($wName, 'wName', $wcName), 'workerExists');

        return $result;
    }

    //      User queue handling:
    /**
     * @brief Checks if the data that is being provided is valid for a queue to be started
     * @param string $sName - name of the service
     * @param string $cName - name of the company
     * @param int $uId - user Id
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

    //      Worker handling:
    /**
     * @brief Checks if the data that is being provided is valid for a worker loged in
     * @param string $wComp - company name
     * @param string $wPass - password for the worker
     * @param string $cn - company name over link
     * @param string $p - hashed password over link
     * 
     * @return bool
     */
    public function workerLoginReady($wComp, $wPass, $cn, $p)
    {
        $words = array($wComp, $wPass);

        $this->Log("Company name $wComp \n");
        $wTableName =  'WORKERS_' . str_replace(' ', '', $wComp);
        $this->Log("Table name $wTableName \n");
        $result = $this->error->onWorkerLoginError($this->areEmpty($words), 'empty', $cn, $p);
        $result = $this->error->onWorkerLoginError($this->areInvalid($words), 'invalid', $cn, $p);
        $result = $this->error->onWorkerLoginError(!$this->findTable($wTableName), 'companyNonExistent', $cn, $p);

        return $result;
    }

    //  Private:
    //      Inspectors (general inspection checkers): 

    /**
     * @brief Checks if the data is empty
     * @param array $words - data of strings
     * 
     * @return bool returns true if one of the strings is empty
     */
    private function areEmpty(array $words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (empty($words[$i])) {
                $this->Log("Empty at " . $i . "\n");
                return true;
            }
        }
        $this->Log("Inputs aren't empty\n");
        return false;
    }

    /**
     * @brief Checks if the data isn't considered invalid
     * @param array $words - data of strings
     * 
     * @return bool returns true if one of the string is invalid
     */
    private function areInvalid($words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (preg_match('/[\^£$%&*()}{#~?><>|=_+¬-]/', $words[$i])) {
                $this->Log("Word: " . $words[$i] . " is empty\n");
                return true;
            }
        }
        $this->Log("Array isn't invalid\n");
        return false;
    }

    /**
     * @brief Checks if the data provided is of a type email
     * @param string $email - email string
     * 
     * @return bool returns true if the email is invalid
     */
    private function isNotEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->Log("Email is invalid $email\n");
            return true;
            //header("Location: ../sites/signup.site.php?signup=invalidemail");
        }
        $this->Log("Email is valid \n");
        return false;
    }

    /**
     * @brief Checks if the data provided already exists in the database
     * @param string $var - the data we are trying to find 
     * @param string $tData - where we are trying to find it
     * @param string $table - in what table should the data be
     * 
     * @return bool returns true if the data has been found
     */
    private function alreadyExists($var, $tData, $table)
    {
        $sql = "SELECT * FROM $table WHERE $tData = $var;";

        if (gettype($var) === "string")
            $sql = "SELECT * FROM $table WHERE $tData = '$var';";


        $row = $this->getStmtRow($sql);

        if ($row[$tData] == $var) {
            $this->Log("Data [$tData] for $var exists! \n");
            return true;
        } else {
            $this->Log("Data [$tData] for $var doesn't exists!\n");
            return false;
        }
    }
}