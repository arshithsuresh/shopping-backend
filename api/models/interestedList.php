<?php

Class InterestedList{

    private $conn;  

    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function removeFavorite($userId,$productId)
    {
        $query = ("call removeFavorite(?,?);");
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1,$userId);
        $stmt->bindParam(2,$productId);        

        if($stmt->execute())
        {            
            return true;
        }

        return false;
    }
    public function setStatus($userId,$productId,$status)
    {
        $query = ("call setStatus(?,?,?);");
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1,$userId);
        $stmt->bindParam(2,$productId);        
        $stmt->bindParam(3,$status);        

        if($stmt->execute())
        {            
            return true;
        }

        return false;
    }
    public function addToInterestedList($userId,$productId,$message)
    {
        $query = ("call addInterestedProduct(?,?,?);");
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1,$userId);
        $stmt->bindParam(2,$productId);
        $stmt->bindParam(3,$message);

        if($stmt->execute())
        {            
            return true;
        }

        return false;
    }

    
}

?>