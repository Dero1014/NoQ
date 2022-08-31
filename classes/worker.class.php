<?php

/**
 * @brief Used for storing data about the worker and 
 * managing queues
 */
class Worker extends SQL
{
    private $wId;
    private $wName;
    private $wPass;
    private $wComp;
    private $wTableName;
    private $cTableName;
    private $myUser = null;

    // Time data
    private $timeStart;
    private $timeEnd;
    private $time;
    private $myAvgTime;
    private $myNumOfUsers;

    /**
     * @brief Constructor for Worker
     * 
     * @param int  $wId - worker id
     * @param string  $wName - worker username
     * @param string  $wPass - worker password
     * @param string  $wComp - company the worker works for
     * 
     * @return void
     */
    public function __construct($wId, $wName, $wPass, $wComp)
    {
        parent::__construct("Worker");
        if ($wId != 0) {
            $this->wId = $wId;
            $this->wName = $wName;
            $this->wPass = $wPass;
            $this->wComp = $wComp;
            $this->cTableName = 'COMPANY_' . str_replace(' ', '', $wComp);
            $this->wTableName = 'WORKERS_' . str_replace(' ', '', $wComp);
            $this->myAvgTime = $this->findAverageTime($wName, $this->wTableName);
            $this->myNumOfUsers = $this->findNumOfUsers($wName, $this->wTableName);
        }
    }

    // Methods:
    //  Private:

    /**
     * @brief Finds average time of the worker from the table
     * 
     * @param string  $wName - worker name
     * @param string  $wTableName - worker table 
     * 
     * @return int averge time
     */
    private function findAverageTime($wName, $wTableName)
    {
        $this->query = $this->connect();

        $sql = "SELECT * FROM $wTableName WHERE wName = '$wName'";
        $row = $this->getStmtRow($sql);

        return (int)$row['avgTime'];
    }


    /**
     * @brief Finds number of users of the worker from the table
     * 
     * @param string  $wName - worker name
     * @param string  $wTableName - worker table 
     * 
     * @return int number of users that the worker has processed
     */
    private function findNumOfUsers($wName, $wTableName)
    {
        $this->query = $this->connect();

        $sql = "SELECT * FROM $wTableName WHERE wName = '$wName'";
        $row = $this->getStmtRow($sql);

        return $row['numberOfUsers'];
    }

    /**
     * @brief Finds the user in the table Users and creates a
     * User class from the data and assigns it to the worker
     * 
     * @param int  $uId - user Id
     * 
     * @return void 
     */
    private function findMyUser($uId)
    {
        $this->query = $this->connect();

        $wTableName = $this->wTableName;
        $wId = $this->wId;

        // Get user
        $sql = "SELECT * FROM Users WHERE uId = $uId;";
        $row = $this->getStmtRow($sql);
        $this->myUser = new User($row['uId'], $row['uName'], $row['uEmail'], $row['uCompany']);

        // Set user in worker
        $sql = "UPDATE $wTableName SET myUser = $uId WHERE wId = $wId;";
        $this->updateTable($sql);
    }

    /**
     * @brief Creates a table name for the worker by using the
     * company name and the prefix "WORKERS_"
     * 
     * @param string  $wComp - Company name
     * 
     * @return string "WORKERS_[company name]"
     */
    private function makeWorkerTableName($wComp)
    {
        $xcName = str_replace(' ', '', $wComp);
        $wTableName = $this->wTableName = 'WORKERS_' . $xcName;
        return $wTableName;
    }

    //      Timer methods:

    /**
     * @brief Starts the timer
     * 
     * @return int returns time in seconds
     */
    private function timerStart()
    {
        return $this->timeStart = time();
    }

    /**
     * @brief Ends the timer
     * 
     * @return int returns time in seconds
     */
    private function timerEnd()
    {
        return $this->timeEnd = time();
    }

    /**
     * @brief The difference between the end timer and start timer
     * 
     * @param int $timeStart - time when the timer started
     * @param int $timeEnd - time when the timer ended
     * 
     * @return int returns the difference of time in seconds
     */
    private function timerResult($timeStart, $timeEnd)
    {
        return $timeEnd - $timeStart;
    }

    /**
     * @brief Updates the table contents regarding time
     * 
     * @param int $time - time difference calculated
     * @param string $sName - service name to be updated
     * 
     * @return void Updates the table contents for service and
     * worker
     */
    private function updateServiceTime($time, $sName)
    {
        $this->query = $this->connect();

        // Company name db
        $cTableName = $this->cTableName;
        $wTableName = $this->wTableName;
        $wName = $this->wName;

        // Update service
        $sql = "UPDATE $cTableName SET timeSum = timeSum + $time 
            WHERE sName = '$sName';";

        $this->updateTable($sql);

        $sql = "UPDATE $cTableName SET avgTime = timeSum / numberOfUsers 
            WHERE sName = '$sName';";

        $this->updateTable($sql);

        // Update worker
        $sql = "UPDATE $wTableName SET timeSum = timeSum + $time 
            WHERE wName = '$wName';";

        $this->updateTable($sql);

        $sql = "UPDATE $wTableName SET avgTime = timeSum / numberOfUsers 
            WHERE wName = '$wName';";

        $this->updateTable($sql);
    }

    //  Public:
    //      Worker functions:
    /**
     * @brief Logs the worker into the site and gives access
     * 
     * @param string $wComp - company name
     * @param string $wPass - worker password
     * @param string $cn - company name from link
     * @param string $p - hashed password from link
     * 
     * @return int
     */
    public function logIn($wComp, $wPass, $cn, $p)
    {
        $wTableName = $this->makeWorkerTableName($wComp);
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

    //      Worker GETS:

    /**
     * @brief Returns workers id
     * 
     * @return int
     */
    public function getWorkerId()
    {
        return $this->wId;
    }

    /**
     * @brief Returns workers name
     * 
     * @return string
     */
    public function getWorkerName()
    {
        return $this->wName;
    }

    /**
     * @brief Returns User object
     * 
     * @return |null|User
     */
    public function getMyUser()
    {
        return $this->myUser;
    }

    /**
     * @brief Returns Worker password
     * 
     * @return string
     */
    public function getWorkerPass()
    {
        return $this->wPass;
    }

    /**
     * @brief Returns the number of users the worker has
     * processed
     * 
     * @return int
     */
    public function getWorkerUserNumber()
    {
        return $this->myNumOfUsers;
    }

    /**
     * @brief Returns the average time the worker spends
     * between users
     * 
     * @return int
     */
    public function getWorkerAverageTime()
    {
        return $this->myAvgTime;
    }

    /**
     * @brief Returns the company name the user works for
     * 
     * @return int
     */
    public function getWorkerCompanyName()
    {
        return $this->wComp;
    }

    //      Queue functions:

    /**
     * @brief Moves the queue to the next User
     * 
     * @param string $sName - service name
     * 
     * @return bool true if it succeeds
     */
    public function nextInQueue($sName)
    {
        $this->query = $this->connect();

        // TODO: Use the function from the company class getServiceQueueTableName 
        $qsTableName = $this->getQueueName($sName);
        $target = null;

        $sql = "SELECT * FROM $qsTableName;";
        $result = $this->getStmtAll($sql);
        for ($i = 0; $i < sizeof($result); $i++) {
            if ($result[$i][3] == 0) {
                $target = $result[$i][2];
                $sql = "UPDATE $qsTableName SET myTurn = 1 WHERE userId = $target;";
                $this->updateTable($sql);
                $this->findMyUser($target);
                $this->timerStart();
                return true;
            }
        }
        return false;
    }

    /**
     * @brief Show the users that are in the queue
     * In case there is a user that is being processed
     * in a different queue by the same worker drop him out
     * of the workers hands
     * 
     * @param string $sName - service name
     * 
     * @return void prints out the users in the queue
     */
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

    /**
     * @brief Remove the user from workers hands
     * 
     * @param int $uId - user Id
     * 
     * @return void 
     */
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

    /**
     * @brief Process user and update tables
     * 
     * @param string $sName - service name
     * 
     * @return bool if the user has been processed return true 
     */
    public function processUser($sName)
    {
        $this->query = $this->connect();

        $qsTableName = $this->getQueueName($sName);
        $cTableName = $this->cTableName;
        $wTableName = $this->wTableName;
        $wName = $this->wName;

        if ($this->myUser == NULL) {
            return false;
        }

        $uId = $this->myUser->getUId();
        $this->dropOut($uId);

        // Remove user from Queues and queue table
        $this->removeStmtValuesFrom($qsTableName, 'userId', $uId);
        $this->removeStmtValuesFrom('Queues', 'userId', $uId);

        // Update companies service
        $sql = "UPDATE $cTableName SET numberOfUsers = numberOfUsers + 1 WHERE sName = '$sName';";
        $this->updateTable($sql);

        // Update worker
        $sql = "UPDATE $wTableName SET numberOfUsers = numberOfUsers + 1 WHERE wName = '$wName';";
        $this->updateTable($sql);

        // TODO: Opt for adding a Queue object to use queueFullnes function
        // Will find a way to use Queue class
        // Get queue table length
        $sql = "SELECT * FROM $qsTableName";
        $result  = $this->getStmtAll($sql);

        // Check size
        if (sizeof($result) == 0) {
            // Drop table
            $this->dropTable($qsTableName);
        }

        // End timer, get result, update service and worker
        $this->updateServiceTime($this->timerResult($this->timeStart, $this->timerEnd()), $sName);

        return true;
    }

    //      Queue GETS:

    /**
     * @brief Returns name of the queue table
     * 
     * @return string
     */
    // TODO: Opt for using from Company class  getServiceQueueTableName
    public function getQueueName($sName)
    {
        return 'QUEUE_' . str_replace(' ', '', $this->wComp) . '_' . str_replace(' ', '', $sName);;
    }

    /**
     * @brief Returns current time that has passed ever since the user
     * has got his turn
     * 
     * @return string
     */
    public function getCurrentTime()
    {
        return $this->timerResult($this->timeStart, $this->timerEnd());
    }
}