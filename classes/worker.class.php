<?php

class Worker
{   
    private $wId;
    private $wName;
    private $wPass;
    private $wComp;

    public function __construct($wId, $wPass, $wComp, $wName)
    {
        $this->wId = $wId;
        $this->wName = $wName;
        $this->wComp = $wComp;
        $this->wPass = $wPass;
    }

    public function getWorkerId()
    {
        return $this->wId;
    }

    public function getWorkerName()
    {
        return $this->wName;
    }

    public function getWorkerPass()
    {
        return $this->wPass;
    }

    public function getWorkerCompanyName()
    {
        return $this->wComp;
    }
}