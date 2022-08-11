<?php
class Inspector extends SQL
{
    // Check result for registering user
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
        if ($uCompany == 1) $result = $result = $this->error->onRegisterError($this->tableExists($cDbName), 'companyExists');
        
        return $result;
    }

    // Check if any input is empty
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

    // Check if the input is invalid
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

    // Check if email is valid
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

    // Check if a STRING already exists for table
    private function alreadyExists($string, $dbData, $db)
    {
        $sql = "SELECT * FROM $db WHERE $dbData = '$string';";

        $row = $this->getStmtRow($sql);

        if ($row[$dbData] == $string) {
            echo "Data [$dbData] for $string exists! \n";
            return true;
        } else {
            echo "Data [$dbData] for $string doesn't exists!\n";
            return false;
        }
    }

     // TableExists
     private function tableExists($cName)
     {
        if ($result = $this->query->query("SHOW TABLES LIKE '".$cName."'")) {
            if($result->num_rows == 1) {
                echo "Table $cName exists\n";
                return true;
            }
        }
        else {
            echo "Table $cName does not exist\n";
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
