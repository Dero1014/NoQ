<?php

class Queue extends SQL
{
    
    public function dropQueueTable($qDbName)
    {
        $this->query = $this->connect();

        // Remove users that are in that Queue
        $this->removeStmtValuesFrom("Queues", "queueName", $qDbName);

         // Remove the queue table itself
        $this->dropTable($qDbName);
        
        return true;
    }
}