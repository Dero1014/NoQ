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
}
