<?php

class Worker
{   
    private $wId;
    private $wName;
    private $wPass;
    private $wComp;

    public function __construct($wId, $wName, $wPass, $wComp)
    {
        $this->wId = $wId;
        $this->wName = $wName;
        $this->wPass = $wPass;
        $this->wComp = $wComp;
    }

    public function getWorkerId()
    {
        return $this->wId;
    }

    public function getWorkerName()
    {
        return $this->wName;
    }

    public function getWorkerPass()
    {
        return $this->wPass;
    }

    public function getWorkerCompanyName()
    {
        return $this->wComp;
    }

    /*
    public function logIn($wPass, $wComp)
    {
        session_start();
        $p = $_SESSION["p"];
        $sql = "SELECT * FROM Workers WHERE wComp = ? AND wPass = ?;";
    
        $stmt = startPrepStmt($conn, $sql);
    
        mysqli_stmt_bind_param($stmt, "ss", $wComp, $p);
        mysqli_stmt_execute($stmt);
    
        $resultData = mysqli_stmt_get_result($stmt);
    
        if ($resultData !== false) {
    
            $checkPwd = checkPwd($resultData, $wPass);
    
            if ($checkPwd === true) {
                // make sure a session has been started
                header("Location: ../sites/worker.site.php?access=granted");
            } else {
                header("Location: ../sites/worker.site.php?access=denied");
                exit();
            }
        } else {
            header("Location: ../login.site.php?signin=fail");
            exit();
        }
    }
    */
}