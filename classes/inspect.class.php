<?php

class Inspector
{
    // ERROR HANDLERS //

    public function registerUserReady($uName, $uPass, $uEmail, $uCompany, $cName, $cDesc)
    {
        $words = [$uName, $uPass, $uEmail, $uCompany, $cName, $cDesc];
        $result =  $this->areEmpty($words);
        $result = $this->areInvalid($words);
        $result = $this->isEmail($uEmail);
        return $result;
    }

    // Check if any input is empty
    private function areEmpty(array $words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (empty($words[$i])) {
                echo "Yes it is empty " . $i . "\n";
                return true;
            }
        }
        echo "No it isn't empty\n";
    }

    // Check if the input is invalid
    private function areInvalid($words)
    {
        for ($i = 0; $i < count($words); $i++) {
            if (preg_match('/[\^£$%&*()}{#~?><>|=_+¬-]/', $words[$i])) {
                echo "it's invalid\n";
                return true;
            }
        }
        echo "it's not invalid\n";
    }

    // Check if email is valid
    private function isEmail($email)
    {
        
    }

    // Check if A STRING already exists
    private function alreadyExists($conn, $string, $dbData, $db)
    {
        $sql = "SELECT * FROM $db WHERE $dbData = ?;";
        $stmt = startPrepStmt($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $string);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($resultData);
        if ($row[$dbData] == $string) {
            echo "It exists\n";
            return true;
        } else {
            echo "It doesn't exist\n";
            return false;
        }

        mysqli_stmt_close($stmt);
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
