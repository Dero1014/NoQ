<?php

/**
 * @brief Holds info about the queue and favors the user side info
 */
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

    // Methods:
    //  Public:

    /**
     * @brief Sets up all of the variables
     * 
     * @param string $cName - company name
     * @param string $sName - serviceName
     * @param int $uId - user id
     * 
     */
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

    /**
     * @brief Queues up the user
     * 
     * @param string $cName - company name
     * @param string $sName - serviceName
     * @param int $uId - user id
     * 
     * @return bool returns true if all goes well
     */
    public function queueUp($cName, $sName, $uId)
    {
        $this->queueSetup($cName, $sName, $uId);

        // Extra variables 
        $qTableName = $this->qTableName;

        // Check if a queue already exists
        $qeueuExists = $this->queueExists($qTableName);
        $currentQueue = ($qeueuExists === true) ? $this->findQueue($qTableName) : 1;

        // Insert user in queue table db
        $sql = "INSERT INTO $qTableName (queue, userId) 
            VALUES (?, ?);";

        $this->setStmtValues("ii", $sql, array($currentQueue, $uId));

        // Insert user in queue
        $sql = "INSERT INTO Queues (userId, queueName, cName, sName) 
            VALUES (?, ?, ?, ?);";

        $this->setStmtValues("isss", $sql, array($uId, $qTableName, $cName, $sName));
        return true;
    }


    /**
     * @brief Drop user from queue
     * 
     * @return bool returns true if all goes well
     */
    // TODO: Place the function into the worker class
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

    /**
     * @brief Check if user is already in a queue
     * 
     * @param int $uId - user Id
     * 
     * @return bool returns true if the user is already in a queue
     */
    public function inQueue($uId)
    {
        // Fetch data from Queues
        $sql = "SELECT * FROM Queues WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);

        if (isset($row['userId'])) {
            $this->queueSetup($row['cName'], $row['sName'], $row['userId']);

            $this->queueNum =  $this->findMyQueue($this->qTableName, $uId);
            $this->positionNum =  $this->findMyPosition($this->qTableName, $this->queueNum);
            $this->myTurn = ($this->findTurn($this->qTableName, $uId) !== 0) ? 1 : 0;

            $this->Log("In queue \n");
            return true;
        } else {
            $this->Log("Not in queue \n");
            return false;
        }
    }

    /**
     * @brief Removes users from Queues that have been registered
     * to a queue table and removes the queue table as well
     * 
     * @param string $qDbName - queue table name
     * 
     * @return void
     */
    public function dropQueueTable($qDbName)
    {
        // Remove users from queue
        $this->removeStmtValuesFrom("Queues", "queueName", $qDbName);

        // Remove queue table
        $this->dropTable($qDbName);
    }

    //      Queue Gets:

    /**
     * @brief Gets the average time of the service
     * 
     * @return int the average time in minutes
     */
    public function getAvgTime()
    {
        $sName = $this->sName;
        $cTableName = $this->cTableName;
        $sql = "SELECT * FROM $cTableName WHERE sName = '$sName'";
        $row = $this->getStmtRow($sql);

        return (int)$row['avgTime'] / 60;
    }

    /**
     * @brief Returns table name of the queue
     * 
     * @return string 
     */
    public function getQueueName()
    {
        return $this->qTableName;
    }

    /**
     * @brief Returns queue number
     * 
     * @return int 
     */
    public function getQueueNumber()
    {
        return $this->queueNum;
    }

    /**
     * @brief Returns queue position
     * 
     * @return int 
     */
    public function getPositionNumber()
    {
        return $this->positionNum;
    }

    /**
     * @brief Returns queue turn
     * 
     * @return int 
     */
    public function getMyTurn()
    {
        return $this->myTurn;
    }

    /**
     * @brief Returns company name
     * 
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->cName;
    }

    /**
     * @brief Returns company table name
     * 
     * @return string 
     */
    public function getCompanyTableName()
    {
        return $this->cTableName;
    }

    /**
     * @brief Returns company service name
     * 
     * @return string 
     */
    public function getServiceName()
    {
        return $this->sName;
    }

    //  Private: 
    /**
     * @brief Check if there is anything left in the queue table
     * if not it deletes the queue
     * 
     * @return bool true if it passes
     */
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

    /**
     * @brief Checks if this queue exists if it doesn't it creates it
     * if it does it returns true
     * 
     * @return bool true if it exists and false if it has to create a new one
     */
    private function queueExists($qTableName)
    {
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

    /**
     * @brief Returns current queue number increased by one for the user 
     * 
     * @param string $qDbName - queue table name
     * 
     * @return int 
     */
    private function findQueue($qDbName)
    {
        $sql = "SELECT * FROM $qDbName ORDER BY qId DESC LIMIT 1;";

        $row = $this->getStmtRow($sql);

        $queue = $row['queue'] + 1;

        return $queue;
    }

    /**
     * @brief Returns current queue number of the user 
     * 
     * @param string $qDbName - queue table name
     * @param int $uId - user id
     * 
     * @return int 
     */
    private function findMyQueue($qDbName, $uId)
    {
        $sql = "SELECT * FROM $qDbName WHERE userId = $uId;";

        $row = $this->getStmtRow($sql);

        $queue = $row['queue'];

        return $queue;
    }

    /**
     * @brief Returns current queue position of the user 
     * 
     * @param string $qDbName - queue table name
     * @param int $queueNum - queue number
     * 
     * @return int 
     */
    private function findMyPosition($qDbName, $queueNum)
    {
        $sql = "SELECT * FROM $qDbName WHERE queue < $queueNum;";

        $result = $this->getStmtAll($sql);

        $position = sizeof($result) + 1;

        return $position;
    }

    /**
     * @brief Returns the turn of the user  
     * 
     * @param string $qDbName - queue table name
     * @param int $uId - user id
     * 
     * @return int 
     */
    private function findTurn($qDbName, $uId)
    {
        $sql = "SELECT * FROM $qDbName WHERE userId = $uId;";

        $row = $this->getStmtRow($sql);

        $myTurn = $row['myTurn'];

        return $myTurn;
    }
}