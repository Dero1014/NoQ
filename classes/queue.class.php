<?php

class Queue extends SQL
{
    private $cName;
    private $sName;
    private $xcName;
    private $xsName;
    private $cTableName;
    private $qTableName;
    private $uId;

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
        $this->queueExists($qTableName);
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
}
