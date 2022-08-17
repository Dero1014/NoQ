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
    private $myTurn;
    private $uId;

    public function getAvgTime()
    {
        $sName = $this->sName;
        $cTableName = $this->cTableName;
        $sql = "SELECT * FROM $cTableName WHERE sName = '$sName'";
        $row = $this->getStmtRow($sql);

        return (int)$row['avgTime'] / 60;
    }

    public function dropQueueTable($qDbName)
    {
        $this->query = $this->connect();

        // Remove users that are in that Queue
        $this->removeStmtValuesFrom("Queues", "queueName", $qDbName);

        // Remove the queue table itself
        $this->dropTable($qDbName);

        return true;
    }

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
        $xcName = $this->xcName;
        $xsName = $this->xsName;
        $cTableName = $this->cTableName;
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

    public function inQueue($uId)
    {
        // Fetch data from Queues
        $sql = "SELECT * FROM Queues WHERE userId = $uId;";
        echo "started";
        $row = $this->getStmtRow($sql);
        if (isset($row['userId'])) {
            $this->queueSetup($row['cName'], $row['sName'], $row['userId']);

            $this->queueNum =  $this->getQueue($this->qTableName) - 1;
            $this->myTurn = ($row['inLine'] !== 0) ? 1 : 0;
            echo "passes ";
            return true;
        } else {
            session_start();
            unset($_SESSION["queue"]);
            unset($_SESSION["inLine"]);
            session_unset($_SESSION["queue"]);
            session_unset($_SESSION["inLine"]);
            echo "no pass ";
            return false;
        }
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
            foreign key (userId) references Users(uId),
            primary key (qId)
            );";

            $this->createTable($qTableName, $contents);

            return false;
        }
    }

    public function getQueueNumber()
    {
        return $this->queueNum;
    }

    public function getMyTurn()
    {
        return $this->myTurn;
    }

    private function getQueue($qDbName)
    {
        $sql = "SELECT * FROM $qDbName ORDER BY qId DESC LIMIT 1;";

        $row = $this->getStmtRow($sql);

        $queue = $row['queue'] + 1;

        return $queue;
    }
}
