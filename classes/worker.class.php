<?php

class Worker extends SQL
{
    private $wId;
    private $wName;
    private $wPass;
    private $wComp;
    private $wTableName;
    private $cTableName;

    public function __construct($wId, $wName, $wPass, $wComp)
    {
        parent::__construct("Worker");
        $this->wId = $wId;
        $this->wName = $wName;
        $this->wPass = $wPass;
        $this->wComp = $wComp;
        $this->cTableName = 'COMPANY_' . str_replace(' ', '', $wComp);
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

    private function getWorkerTableName($wComp)
    {
        $xcName = str_replace(' ', '', $wComp);
        $wTableName = $this->wTableName = 'WORKERS_' . $xcName;
        return $wTableName;
    }


    public function logIn($wComp, $wPass, $cn, $p)
    {
        $wTableName = $this->getWorkerTableName($wComp);
        $sql = "SELECT * FROM $wTableName;";
        $row =  $this->getStmtRow($sql);

        if ($row['wPass'] === $p) {

            $checkPwd = password_verify($wPass, $p);

            if ($checkPwd === true) {
                $worker = new Worker($row['wId'], $row['wName'], $wPass, $wComp);
                session_start();
                $_SESSION['worker'] = $worker;
                header("Location: ../sites/worker.site.php?access=granted");
                exit();
            } else {
                header("Location: ../sites/worker.site.php?cn=$cn&p=$p&access=denied");
                exit();
            }
        } else {
            header("Location: ../sites/worker.site.php?cn=$cn&p=$p&login=wrongCompany");
            exit();
        }
    }

    // QUEUE PROCESS METHODS
    public function nextInQueue($sName)
    {
        $this->query = $this->connect();
        $qsTableName = 'QUEUE_' . str_replace(' ', '', $this->wComp). '_' . str_replace(' ', '', $sName);
        $sql = "SELECT * FROM $qsTableName;";
        $result = $this->getStmtAll($sql);
        $target = null;
        for ($i=0; $i < sizeof($result); $i++) { 
            if ($result[$i][3] == 0) {
                $target = $result[$i][2];
                $sql = "UPDATE $qsTableName SET myTurn = 1 WHERE userId = $target;";
                $this->updateTable($sql);
                return true;
            }
        }
        return false;
    }

    public function showQueue($sName)
    {
        $this->query = $this->connect();
        $qsTableName = 'QUEUE_' . str_replace(' ', '', $this->wComp). '_' . str_replace(' ', '', $sName);
        $sql = "SELECT * FROM $qsTableName JOIN Users ON userId = Users.uId;";
        // Result for name is on 5 just add worker columns and user columns and
        // stick it next to each other
        $result = $this->getStmtAll($sql);
        var_dump($result);
        for ($i=0; $i < sizeof($result); $i++) { 
            $uName = $result[$i][5];
            echo "<p>User: $uName</p>";
        }

    }
}
