<?php

class Worker extends SQL
{
    private $wId;
    private $wName;
    private $wPass;
    private $wComp;
    private $wTableName;
    private $cTableName;
    private $myUser = null;

    private $timeStart;
    private $timeEnd;
    private $time;

    public function __construct($wId, $wName, $wPass, $wComp)
    {
        parent::__construct("Worker");
        $this->wId = $wId;
        $this->wName = $wName;
        $this->wPass = $wPass;
        $this->wComp = $wComp;
        $this->cTableName = 'COMPANY_' . str_replace(' ', '', $wComp);
        $this->wTableName = 'WORKERS_' . str_replace(' ', '', $wComp);
    }

    public function getWorkerId()
    {
        return $this->wId;
    }

    public function getWorkerName()
    {
        return $this->wName;
    }

    public function getMyUser()
    {
        return $this->myUser;
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
        $result =  $this->getStmtAll($sql);
        for ($i = 0; $i < sizeof($result); $i++) {
            if ($result[$i][2] === $p) {
                $checkPwd = password_verify($wPass, $p);

                if ($checkPwd === true) {
                    $worker = new Worker($result[$i][0], $result[$i][1], $wPass, $wComp);
                    session_start();
                    $_SESSION['worker'] = $worker;
                    header("Location: ../sites/worker.site.php?access=granted");
                    exit();
                } else {
                    header("Location: ../sites/worker.site.php?cn=$cn&p=$p&access=denied");
                    exit();
                }
            }
        }

        header("Location: ../sites/worker.site.php?cn=$cn&p=$p&login=badPass");
        exit();
    }

    private function findMyUser($uId)
    {
        $this->query = $this->connect();
        $wTableName = $this->wTableName;
        $wId = $this->wId;

        // Get user
        $sql = "SELECT * FROM Users WHERE uId = $uId;";
        $row = $this->getStmtRow($sql);
        $myUser = new User($row['uId'], $row['uName'], $row['uEmail'], $row['uCompany']);

        // Set user in worker
        $sql = "UPDATE $wTableName SET myUser = $uId WHERE wId = $wId;";
        $this->updateTable($sql);

        return $myUser;
    }

    // QUEUE PROCESS METHODS
    public function nextInQueue($sName)
    {
        $this->query = $this->connect();
        $qsTableName = 'QUEUE_' . str_replace(' ', '', $this->wComp) . '_' . str_replace(' ', '', $sName);
        $sql = "SELECT * FROM $qsTableName;";
        $result = $this->getStmtAll($sql);
        $target = null;
        for ($i = 0; $i < sizeof($result); $i++) {
            if ($result[$i][3] == 0) {
                $target = $result[$i][2];
                $sql = "UPDATE $qsTableName SET myTurn = 1 WHERE userId = $target;";
                $this->updateTable($sql);
                $this->myUser = $this->findMyUser($target);
                $this->timerStart();
                return true;
            }
        }
        return false;
    }

    public function showQueue($sName)
    {
        $this->query = $this->connect();
        $qsTableName = $this->getQueueName($sName);

        // Is a user active
        if ($this->myUser != NULL) {
            
            // Find out if this user is in the current queue
            $uId = $this->myUser->getUId();
            $sql = "SELECT * FROM $qsTableName WHERE userId = $uId;";
            $row = $this->getStmtRow($sql);
            if (!isset($row['userId'])) {
                $this->dropOut($uId);
            }
        }

        $sql = "SELECT * FROM $qsTableName JOIN Users ON userId = Users.uId;";

        // Result for name is on 5 just add worker columns and user columns and
        // stick it next to each other
        $result = $this->getStmtAll($sql);
        for ($i = 0; $i < sizeof($result); $i++) {
            $uName = $result[$i][5];
            if ($result[$i][3] != 1) {
                echo "<p>User: $uName</p>";
            }
        }
    }

    public function dropOut($uId)
    {
        $this->query = $this->connect();
        $wTableName  = $this->wTableName;

        // Find the queue the user is in
        $sql = "SELECT * FROM Queues WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);
        $qPreviousTableName = $row['queueName'];

        // Release it from the worker
        $sql = "UPDATE $qPreviousTableName SET myTurn = 0 WHERE userId = $uId;";
        $this->updateTable($sql);

        // Release the worker from the user 
        $sql = "UPDATE $wTableName SET myUser = NULL WHERE myUser = $uId;";
        $this->updateTable($sql);

        // Remove user variable
        $this->myUser = NULL;
    }

    public function processUser($sName)
    {
        $this->query = $this->connect();
        $qsTableName = $this->getQueueName($sName);
        $cTableName = $this->cTableName;
        if ($this->myUser == NULL) {
            return false;
        }

        $uId = $this->myUser->getUId();
        $this->dropOut($uId);

        $this->removeStmtValuesFrom($qsTableName, 'userId', $uId);
        $this->removeStmtValuesFrom('Queues', 'userId', $uId);

        // Update companies service
        $sql = "UPDATE $cTableName SET numberOfUsers = numberOfUsers + 1 WHERE sName = '$sName';";
        $this->updateTable($sql);

        // Will find a way to use Queue class
        // Get queue table length
        $sql = "SELECT * FROM $qsTableName";
        $result  = $this->getStmtAll($sql);

        // Check size
        if (sizeof($result) == 0) {
            // Drop table
            $this->dropTable($qsTableName);
        }

        // End timer, get result and update service
        $this->updateServiceTime($this->timerResult($this->timeStart, $this->timerEnd()), $sName);

        return true;
    }

    public function getQueueName($sName)
    {
        return 'QUEUE_' . str_replace(' ', '', $this->wComp) . '_' . str_replace(' ', '', $sName);;
    }

    public function getCurrentTime()
    {
        return $this->timerResult($this->timeStart, $this->timerEnd());
    }

    // Service timing
    private function timerStart()
    {
        return $this->timeStart = time();
    }

    private function timerEnd()
    {
        return $this->timeEnd = time();
    }

    private function timerResult($timeStart, $timeEnd)
    {
        return $timeEnd - $timeStart;
    }

    private function updateServiceTime($time, $sName)
    {
        $this->query = $this->connect();
        // Company name db
        $cTableName = $this->cTableName;

        $sql = "UPDATE $cTableName SET timeSum = timeSum + $time 
            WHERE sName = '$sName';";

        $this->updateTable($sql);

        $sql = "UPDATE $cTableName SET avgTime = timeSum / numberOfUsers 
            WHERE sName = '$sName';";

        $this->updateTable($sql);
    }
}
