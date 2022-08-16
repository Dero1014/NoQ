<?php
// Holds general user info

class User extends SQL
{
    private $uId;
    private $uName;
    private $uEmail;
    private $uCompany;
    private $company;

    public function __construct(int $uId, string $uName, string $uEmail, int $uCompany)
    {
        parent::__construct("User");

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
        }
    }

    // Fetch company
    /**
     * @brief Grabs the company associated with the user from table Companies
     * @param int  $uId
     * 
     * @return void
     */
    private function fetchCompany($uId)
    {
        //get company name
        $this->query = $this->connect();
        $sql = "SELECT * FROM Companies WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);
        $this->company = new Company($row['cId'], $row['cName'], $row['xcName'], $row['cDesc']);
    }

    // Get username
    /**
     * @brief Returns the username of the user
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->uName;
    }

    // Get company object
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

    // Get company tag
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

    // Get user id
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
    private $cDesc;
    private array $services = [];

    public function __construct($cId, $cName, $xcName, $cDesc)
    {
        parent::__construct("Company");

        $this->cId = $cId;
        $this->cName = $cName;
        $this->cDesc = $cDesc;
        $this->xcName = $xcName;
        $this->cTableName = 'COMPANY_' . $this->xcName;

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
        $this->services=[];
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
            if ( $id == $service->getSId()) {
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
