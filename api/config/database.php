<?php

class database
{
    private $host ="localhost";
    private $dbName = "topzone";
    private $username ="root";
    private $password ="";

    public $conn;

    public function  getConnection()
    {
        $this->conn =null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->dbName, $this->username, $this->password);

        }catch(PDOException $exception)
        {
            echo "Connection Error : ".$exception->getMessage();
        }

        return $this->conn;
    }

    public function __destruct()
    {        
        $this->Conn = null;        
    }
}

?>