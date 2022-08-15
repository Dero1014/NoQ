<?php
class User extends SQL
{
    private $uId;
    private $uName;
    private $uEmail;
    private $uCompany;
    //private Company $company;

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
                $this->company = NULL;
            }
        }
    }

    private function fetchCompany($uId)
    {
        //get company name
        $sql = "SELECT * FROM Companies WHERE userId = $uId;";
        $row = $this->getStmtRow($sql);
        $this->company = new Company($row['cId'], $row['cName'], $row['xcName'], $row['cDesc']);
    }

    public function getUsername()
    {
        return $this->uName;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function getCompanyTag()
    {
        return $this->uCompany;
    }

    public function getUId()
    {
        return $this->uId;
    }
}

class Company extends SQL
{
    private $cId;
    private $cName;
    private $xcName; // company name without spaces
    private $cTableName; // COMPANY_ + $xcName
    private $cDesc;
    private Service $services;

    public function __construct($cId, $cName, $xcName, $cDesc)
    {
        parent::__construct("Company");

        $this->cId = $cId;
        $this->cName = $cName;
        $this->cDesc = $cDesc;
        $this->xcName = $xcName;
        $this->cTableName = 'COMPANY_' . $this->xcName;

        $services = $this->fetchServices();
    }

    private function fetchServices()
    {
    }

    public function setService($sName)
    {
        $this->query = $this->connect();
        $cDbName = $this->cTableName;
        $sql = "INSERT INTO $cDbName (sName) VALUES (?);";
        $this->setStmtValues("s", $sql, array($sName));

        return true;
    }

    public function getCompanyName()
    {
        return $this->cName;
    }

    public function getNoSpaceCompanyName()
    {
        return $this->cName;
    }

    public function getCompanyTableName()
    {
        return $this->cTableName;
    }
}

class Service
{
    private $sId;
    private $sName;
    private $numberOfUsers;
    private $avgTime;
    private $timeSum;

    public function __construct($sId, $sName, $numberOfUsers, $avgTime, $timeSum)
    {
        $this->sId = $sId;
        $this->sName = $sName;
        $this->numberOfUsers = $numberOfUsers;
        $this->avgTime = $avgTime;
        $this->timeSum = $timeSum;
    }
}
