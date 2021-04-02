<?php

    Class Product{

        private $conn;
        private $tableName="products";

        //Fields
        public $productid;
        public $productname;
        public $stock=1;
        public $addedDate;
        public $upvotes;
        public $shortdecs;
        public $description;
        public $spec_neck;
        public $spec_length;
        public $spec_occasion;
        public $images;


        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function getImages(){
            return $this->images;
        }

        public function getProductById($id){

            $query = "select * from ".$this->tableName." where productid = ?";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1,$id);
            $productData = "{}";
            if($stmt->execute())
            {
                $rowCount = $stmt->rowCount();
                if($rowCount>0)
                {
                    $productData = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                $stmt->closeCursor();                
            }

            return $productData;
        }


    }

?>