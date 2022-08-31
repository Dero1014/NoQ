<?php

/**
 * @brief Acts as a storage for User values
 */
class User extends SQL
{
    private $uId;
    private $uName;
    private $uEmail;
    private $uCompany;
    private $company;

    /**
     * @brief Constructor for user
     * 
     * @param int  $uId - Set to -1 if you don't wanna grab the arguments of the constructor
     * @param string  $uName - username
     * @param string  $uEmail - email
     * @param int  $uCompany - company tag
     * @param bool  $fetchByName - if true will use fetchUserByName method
     * 
     * @return void
     */
    public function __construct(int $uId, string $uName, string $uEmail, int $uCompany, bool $fetchByName = false)
    {
        parent::__construct("User with id $uId");

        // Fetch user data by name
        if ($fetchByName == true) {
            $this->fetchUserByName($uName);
        }

        // If ID is -1 it is considered a null call
        if ($uId != -1) {
            $this->uId = $uId;
            $this->uName = $uName;
            $this->uEmail = $uEmail;
            $this->uCompany = $uCompany;
            if ($uCompany === 1) {
                $this->fetchCompany($uId);
            } else {
                $this->company = new stdClass();
            }
        }else {
            $this->uId = NULL;
            $this->uName = NULL;
            $this->uEmail = NULL;
            $this->uCompany = NULL;
            $this->company = new stdClass();
        }
    }

    // Methods:
    //  Private:

    /**
     * @brief Grabs all of the information about the user 
     * using it's username
     * @param string  $uName - Username
     * 
     * @return void
     */
    private function fetchUserByName($uName)
    {
        //get company name
        $this->query = $this->connect();
        $sql = "SELECT * FROM Users WHERE uName = '$uName';";
        $row = $this->getStmtRow($sql);
        $this->uId = $row['uId'];
        $this->uName = $row['uName'];
        $this->uEmail = $row['uEmail'];
        $this->uCompany = $row['uCompany'];
    }

    /**
     * @brief Grabs the company associated with the user from table Companies
     * and creates a company class with it's data
     * @param int  $uId - user id
     * 
     * @return void
     */
    private function fetchCompany($uId)
    {
        $this->query = $this->connect();

        // Get company name
        $sql = "SELECT * FROM Companies WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);

        // Set company class
        $this->company = new Company($row['cId'], $row['cName'], $row['xcName'], $row['cDesc']);
    }

    //  Public:
    //      Get methods:

    /**
     * @brief Returns the username of the user
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->uName;
    }

    /**
     * @brief Returns the company object, conidered empty if it's 
     *        type of stdClass
     * 
     * @return |company|stdclass
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @brief Returns a company tag that tells if the user owns a company 
     *        or not
     * 
     * @return int
     */
    public function getCompanyTag()
    {
        return $this->uCompany;
    }

    /**
     * @brief Returns the user id from table Users
     * 
     * @return int
     */
    public function getUId()
    {
        return $this->uId;
    }
}

// Holds info about the company and can change some data
class Company extends SQL
{
    private $cId;
    private $cName;
    private $xcName; // company name without spaces
    private $cTableName; // COMPANY_ + $xcName
    private $wcName; // WORKERS_ + $xcName
    private $cDesc;
    private array $services = [];
    private array $workers = [];

    /**
     * @brief Constructor for Company
     * 
     * @param int  $cId - Company id
     * @param string  $cName - username
     * @param string  $xcName - email
     * @param string  $cDesc - company tag
     * 
     * @return void
     */
    public function __construct($cId, $cName, $xcName, $cDesc)
    {
        parent::__construct("Company");

        $this->cId = $cId;
        $this->cName = $cName;
        $this->cDesc = $cDesc;
        $this->xcName = $xcName;
        $this->cTableName = 'COMPANY_' . $this->xcName;
        $this->wcName = 'WORKERS_' . $this->xcName;

        $this->fetchServices();
        $this->fetchWorkers();
    }

    // Methods:
    //  Public:

    /**
     * @brief Grabs the services from the company creates Service class
     * for each one and stores them in an array
     * 
     * @return void
     */
    public function fetchServices()
    {
        $this->query = $this->connect();

        // Set data
        $this->services = [];
        $cDbName = $this->cTableName;
        $service = NULL;

        // Grab services
        $sql = "SELECT * FROM $cDbName";
        $result = $this->getStmtAll($sql);

        for ($i = 0; $i < sizeof($result); $i++) {
            $service = new Service(
                $result[$i][0],
                $result[$i][1],
                $result[$i][2],
                $result[$i][3],
                $result[$i][4]
            );
            array_push($this->services, $service);
        }
    }

    //  Public:
    //      Service side:
    /**
     * @brief Inserts a service to the company
     * 
     * @param string $sName - service name
     * 
     * @return bool returns true if it passes
     */
    public function setService($sName)
    {
        $this->query = $this->connect();

        $cDbName = $this->cTableName;

        $sql = "INSERT INTO $cDbName (sName) VALUES (?);";
        $this->setStmtValues("s", $sql, array($sName));

        // Update services
        $this->fetchServices();

        return true;
    }

    /**
     * @brief Removes service from company
     * 
     * @param int $sId - service id
     * 
     * @return bool
     */
    public function removeService($sId)
    {
        $this->query = $this->connect();
        
        $cDbName = $this->cTableName;
        $tableData = "sId";

        $this->removeStmtValuesFrom($cDbName, $tableData, $sId);

        // Update services
        $this->fetchServices();

        return true;
    }

    //      Service GETS:

    /**
     * @brief Returns the length of service array
     * 
     * @return int - length of the service array
     */
    public function getServiceLength()
    {
        return sizeof($this->services);
    }

    /**
     * @brief Returns a service based on arrays index
     * 
     * @param int $i - index of array
     * 
     * @return Service
     */
    public function getService($i)
    {
        return $this->services[$i];
    }

    /**
     * @brief Returns a service based on services id
     * 
     * @param int $id - id of the service
     * 
     * @return Service
     */
    public function getServiceById($sId)
    {
        foreach ($this->services as $service) {
            if ($sId == $service->getSId()) {
                return $service;
            }
        }
    }

    /**
     * @brief Returns the queue table name of the associated service
     * 
     * @param int $sId - service id
     * 
     * @return string - returns queue table name "QUEUE_[company name]_[service name]"
     */
    public function getServiceQueueTableName($sId)
    {
        $service = $this->getServiceById($sId);
        $xsName = $service->getNoSpaceServiceName();
        $xcName = $this->xcName;

        $qsName = "QUEUE_" . $xcName . "_" . $xsName;
        return $qsName;
    }

    //      Worker side:

    /**
     * @brief Fetches workers from the Workers table that
     *        are part of the company and makes a Worker 
     * class for each one and stores it in the array
     * 
     * @return void
     */
    public function fetchWorkers()
    {
        $this->query = $this->connect();

        $this->workers = [];
        $wcName = $this->wcName;
        $worker = NULL;

        $sql = "SELECT * FROM $wcName;";
        $result = $this->getStmtAll($sql);

        for ($i = 0; $i < sizeof($result); $i++) {
            $worker = new Worker(
                $result[$i][0],
                $result[$i][1],
                $result[$i][2],
                $this->cName
            );
            array_push($this->workers, $worker);
        }
    }

    /**
     * @brief Inserts worker to the Workers table
     * 
     * @return bool
     */
    public function setWorker($rngPass, $wName)
    {
        $this->query = $this->connect();

        $wcName = $this->wcName;

        $sql = "INSERT INTO $wcName (wName, wPass) VALUES (?, ?);";
        $this->setStmtValues("ss", $sql, array($wName, $rngPass));

        return true;
    }

    /**
     * @brief Removes worker from company
     * 
     * @param int $wId - worker id
     * 
     * @return bool
     */
    public function removeWorker($wId)
    {
        $this->query = $this->connect();

        $tableData = "wId";

        $this->removeStmtValuesFrom($this->wcName, $tableData, $wId);
        $this->fetchServices();

        return true;
    }

    //      Worker GETS:
    /**
     * @brief Returns the length of the workers array
     * 
     * @return int
     */
    public function getWorkerLength()
    {
        return sizeof($this->workers);
    }

    // Get worker
    /**
     * @brief Returns a worker on index
     * 
     * @param int $i - index of array
     * 
     * @return Worker
     */
    public function getWorker($i)
    {
        return $this->workers[$i];
    }

    /**
     * @brief Returns worker table name
     * 
     * @return Worker
     */
    public function getWorkerTableName()
    {
        return $this->wcName;
    }

    //      Company GETS:

    /**
     * @brief Returns the company name
     * 
     * @return string
     */
    public function getCompanyName()
    {
        return $this->cName;
    }

    /**
     * @brief Returns the company name without spaces
     * 
     * @return string
     */
    public function getNoSpaceCompanyName()
    {
        return $this->xcName;
    }

    /**
     * @brief Returns the company table name
     * 
     * @return string
     */
    public function getCompanyTableName()
    {
        return $this->cTableName;
    }
}

/**
 * @brief Contains info about a service
 */
class Service
{
    private $sId;
    private $sName;
    private $xsName;
    private $numberOfUsers;
    private $avgTime;
    private $timeSum;

    /**
     * @brief Constructor for User
     * 
     * @param int  $sId - service id
     * @param string  $sName - username
     * @param int  $numberOfUsers - number of users passed through the service
     * @param int  $avgTime - time spent per user
     * @param int $timeSum - sum of time between each user
     * 
     * @return void
     */
    public function __construct($sId, $sName, $numberOfUsers, $avgTime, $timeSum)
    {
        $this->sId = $sId;
        $this->sName = $sName;
        $this->xsName = str_replace(' ', '', $sName);
        $this->numberOfUsers = $numberOfUsers;
        $this->avgTime = $avgTime;
        $this->timeSum = $timeSum;
    }

    // Methods: 
    //  Public:

    /**
     * @brief Returns the service id
     * 
     * @return int
     */
    public function getSId()
    {
        return $this->sId;
    }

    /**
     * @brief Returns the service name
     * 
     * @return string
     */
    public function getServiceName()
    {
        return $this->sName;
    }

    /**
     * @brief Returns the service name without spaces
     * 
     * @return string
     */
    public function getNoSpaceServiceName()
    {
        return $this->xsName;
    }

    /**
     * @brief Returns the number of users from the service
     * 
     * @return int
     */
    public function getServiceNumber()
    {
        return $this->numberOfUsers;
    }

    /**
     * @brief Returns the average time of waiting in the service
     * 
     * @return int
     */
    public function getServiceTime()
    {
        return $this->avgTime;
    }
}