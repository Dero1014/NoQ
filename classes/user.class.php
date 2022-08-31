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

    // Constructor
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
    }

    // Fetch services
    /**
     * @brief Grabs the services from the company and stores
     *        them in an array
     * 
     * @return void
     */
    public function fetchServices()
    {
        $this->services = [];
        $this->query = $this->connect();
        $cDbName = $this->cTableName;
        $sql = "SELECT * FROM $cDbName";
        $result = $this->getStmtAll($sql);
        $service = NULL;
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
        //var_dump($this->services);
        //die();
    }

    // Set service
    /**
     * @brief Adds service to the company
     * 
     * @return bool
     */
    public function setService($sName)
    {
        $this->query = $this->connect();
        $cDbName = $this->cTableName;
        $sql = "INSERT INTO $cDbName (sName) VALUES (?);";
        $this->setStmtValues("s", $sql, array($sName));

        return true;
    }

    // Remove service
    /**
     * @brief Removes service from company
     * 
     * @return bool
     */
    public function removeService($sId)
    {
        $this->query = $this->connect();
        $cDbName = $this->cTableName;
        $tableData = "sId";
        $this->removeStmtValuesFrom($cDbName, $tableData, $sId);
        $this->fetchServices();

        return true;
    }

    // Get service length
    /**
     * @brief Returns the length of service array
     * 
     * @return int
     */
    public function getServiceLength()
    {
        return sizeof($this->services);
    }

    // Get service
    /**
     * @brief Returns a service on index
     * 
     * @return Service
     */
    public function getService($i)
    {
        return $this->services[$i];
    }

    // Get service by Id
    /**
     * @brief Returns a service on index
     * 
     * @return Service
     */
    public function getServiceById($id)
    {
        foreach ($this->services as $service) {
            if ($id == $service->getSId()) {
                return $service;
            }
        }
    }

    // Get service queue name
    /**
     * @brief Returns the queue table name of the associated service
     * 
     * @return string
     */
    public function getServiceQueueTableName($id)
    {
        $service = $this->getServiceById($id);
        $xsName = $service->getNoSpaceServiceName();
        $xcName = $this->xcName;
        $qsName = "QUEUE_" . $xcName . "_" . $xsName;
        return $qsName;
    }

    // Fetch workers
    /**
     * @brief Fetches workers from the Workers table that
     *        are part of the company
     * 
     * @return void
     */
    public function fetchWorkers()
    {
        $this->workers = [];
        $this->query = $this->connect();
        $wcName = $this->wcName;
        $sql = "SELECT * FROM $wcName;";
        $result = $this->getStmtAll($sql);
        $worker = NULL;
        for ($i = 0; $i < sizeof($result); $i++) {
            $worker = new Worker(
                $result[$i][0],
                $result[$i][1],
                $result[$i][2],
                $this->cName
            );
            array_push($this->workers, $worker);
        }
        //var_dump($this->services);
        //die();
    }

    // Set worker
    /**
     * @brief Adds worker to the Workers table
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

    // Remove service
    /**
     * @brief Removes service from company
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

    // Get worker array length
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
     * @return Worker
     */
    public function getWorker($i)
    {
        return $this->workers[$i];
    }

    // Get worker table name
    /**
     * @brief Returns worker table name
     * 
     * @return Worker
     */
    public function getWorkerTableName()
    {
        return $this->wcName;
    }

    // Get company name
    /**
     * @brief Returns the company name
     * 
     * @return string
     */
    public function getCompanyName()
    {
        return $this->cName;
    }

    // Get company name without spaces
    /**
     * @brief Returns the company name without spaces
     * 
     * @return string
     */
    public function getNoSpaceCompanyName()
    {
        return $this->xcName;
    }

    // Get company table name
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

// Holds info about the service
class Service
{
    private $sId;
    private $sName;
    private $xsName;
    private $numberOfUsers;
    private $avgTime;
    private $timeSum;

    public function __construct($sId, $sName, $numberOfUsers, $avgTime, $timeSum)
    {
        $this->sId = $sId;
        $this->sName = $sName;
        $this->xsName = str_replace(' ', '', $sName);
        $this->numberOfUsers = $numberOfUsers;
        $this->avgTime = $avgTime;
        $this->timeSum = $timeSum;
    }

    // Get service id
    /**
     * @brief Returns the service id
     * 
     * @return int
     */
    public function getSId()
    {
        return $this->sId;
    }

    // Get service name
    /**
     * @brief Returns the service name
     * 
     * @return string
     */
    public function getServiceName()
    {
        return $this->sName;
    }

    // Get service name without spaces
    /**
     * @brief Returns the service name without spaces
     * 
     * @return string
     */
    public function getNoSpaceServiceName()
    {
        return $this->xsName;
    }

    // Get service number of users
    /**
     * @brief Returns the number of users from the service
     * 
     * @return int
     */
    public function getServiceNumber()
    {
        return $this->numberOfUsers;
    }

    // Get service average time
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
