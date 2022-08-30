<?php

class Queue extends SQL
{
    private $cName;
    private $sName;
    private $xcName;
    private $xsName;
    private $cTableName;
    private $qTableName;
    private $queueNum;
    private $positionNum;
    private $myTurn;
    private $uId;

    public function queueSetup($cName, $sName, $uId)
    {
        $this->cName = $cName;
        $this->sName = $sName;
        $this->uId = $uId;

        $this->xcName = str_replace(' ', '', $cName);
        $this->xsName = str_replace(' ', '', $sName);

        $this->cTableName = "COMPANY_" . $this->xcName;
        $this->qTableName = "QUEUE_" . $this->xcName . "_" . $this->xsName;
    }

    public function queueUp($cName, $sName, $uId)
    {
        $this->queueSetup($cName, $sName, $uId);

        // Extra variables 
        $qTableName = $this->qTableName;

        // Check if a queue already exists
        $qeueuExists = $this->queueExists($qTableName);
        $currentQueue = ($qeueuExists === true) ? $this->getQueue($qTableName) : 1;

        // Insert user in queue table db
        $sql = "INSERT INTO $qTableName (queue, userId) 
            VALUES (?, ?);";

        $this->setStmtValues("ii", $sql, array($currentQueue, $uId));

        // Insert user in queue
        $sql = "INSERT INTO Queues (userId, queueName, cName, sName) 
            VALUES (?, ?, ?, ?);";

        $this->setStmtValues("isss", $sql, array($uId, $qTableName, $cName, $sName));
    }

    public function dropFromQueue()
    {
        $uId = $this->uId;
        $qTableName = $this->qTableName;
        // Remove User from Queues
        $this->removeStmtValuesFrom("Queues", "userId", $uId);

        // Remove user from queue table
        $this->removeStmtValuesFrom($qTableName, "userId", $uId);

        $this->queueFullnes();
        return true;
    }

    public function inQueue($uId)
    {
        // Fetch data from Queues
        $sql = "SELECT * FROM Queues WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);

        if (isset($row['userId'])) {
            $this->queueSetup($row['cName'], $row['sName'], $row['userId']);

            $this->queueNum =  $this->getMyQueue($this->qTableName, $uId);
            $this->positionNum =  $this->getMyPosition($this->qTableName, $this->queueNum);
            $this->myTurn = ($this->getTurn($this->qTableName, $uId) !== 0) ? 1 : 0;

            $this->Log("In queue \n");
            return true;
        } else {
            $this->Log("Not in queue \n");
            return false;
        }
    }

    public function dropQueueTable($qDbName)
    {
        // Remove users from queue
        $this->removeStmtValuesFrom("Queues", "queueName", $qDbName);

        // Remove queue table
        $this->dropTable($qDbName);
    }

    private function queueFullnes()
    {
        $qTableName = $this->qTableName;
        // Get queue table length
        $sql = "SELECT * FROM $qTableName";
        $result  = $this->getStmtAll($sql);

        // Check size
        if (sizeof($result) == 0) {
            // Drop table
            $this->dropTable($qTableName);
        }

        return true;
    }

    public function getAvgTime()
    {
        $sName = $this->sName;
        $cTableName = $this->cTableName;
        $sql = "SELECT * FROM $cTableName WHERE sName = '$sName'";
        $row = $this->getStmtRow($sql);

        return (int)$row['avgTime'] / 60;
    }

    public function getQueueName()
    {
        return $this->qTableName;
    }

    public function getQueueNumber()
    {
        return $this->queueNum;
    }

    public function getPositionNumber()
    {
        return $this->positionNum;
    }

    public function getMyTurn()
    {
        return $this->myTurn;
    }

    private function queueExists($qTableName)
    {
        //$this->query = $this->connect();
        $result = $this->findTable($qTableName);

        if ($result) {
            return true;
        } else {
            // queue is for the queue number that is gonna be updated
            // userId is for the user that is part of that queue
            $contents = "(
            qId int not null auto_increment,
            queue int not null,
            userId int,
            myTurn bit default 0,
            foreign key (userId) references Users(uId),
            primary key (qId)
            );";

            $this->createTable($qTableName, $contents);

            return false;
        }
    }

    private function getQueue($qDbName)
    {
        $sql = "SELECT * FROM $qDbName ORDER BY qId DESC LIMIT 1;";

        $row = $this->getStmtRow($sql);

        $queue = $row['queue'] + 1;

        return $queue;
    }

    private function getMyQueue($qDbName, $uId)
    {
        $sql = "SELECT * FROM $qDbName WHERE userId = $uId;";

        $row = $this->getStmtRow($sql);

        $queue = $row['queue'];

        return $queue;
    }

    private function getMyPosition($qDbName, $queueNum)
    {
        $sql = "SELECT * FROM $qDbName WHERE queue < $queueNum;";

        $result = $this->getStmtAll($sql);

        $position = sizeof($result) + 1;

        return $position;
    }

    private function getTurn($qDbName, $uId)
    {
        $sql = "SELECT * FROM $qDbName WHERE userId = $uId;";

        $row = $this->getStmtRow($sql);

        $myTurn = $row['myTurn'];

        return $myTurn;
    }
}
