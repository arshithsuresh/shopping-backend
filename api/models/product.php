<?php

    Class ProductModel{

        protected $tableName="products";
        protected $fileStorageRoot = "images/productImages/";

        //Fields
        public $productid;
        public $productcode;
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
        public $thumbnailImg;
        public $newArrival;

        public static function fromDatabase($dbRow){

            $product = new ProductModel();

            $product->productid = $dbRow['productid'];
            $product->productname = $dbRow['productname'];
            $product->productcode = $dbRow['productcode'];
            $product->upvotes = isset($dbRow['upvotes'])?$dbRow['upvotes']:null;
            $product->stock = isset($dbRow['stock'])?$dbRow['stock']:null;
            $product->addedDate = isset($dbRow['addedDate'])?$dbRow['addedDate']:null;
            $product->shortdecs = $dbRow['shortdecs'];
            $product->description = isset($dbRow['description'])?$dbRow['description']:null;
            $product->spec_neck = isset($dbRow['spec_neck'])?$dbRow['spec_neck']:null;
            $product->spec_length = isset($dbRow['spec_length'])?$dbRow['spec_length']:null;
            $product->spec_occasion = isset($dbRow['spec_occasion'])?$dbRow['spec_occasion']:null;
            $product->images = isset($dbRow['images'])?$dbRow['images']:null;
            $product->thumbnailImg = isset($dbRow['thumbnailImg'])?$dbRow['thumbnailImg']:null; 
            
            if(isset($dbRow['addedDate']))
            {
                
                $newArrThreshold = new DateTime(date('Y-m-d', strtotime('-14 day', strtotime(date("Y-m-d")))));                
                $productDate = new DateTime($product->addedDate);

                $product->newArrival = $productDate > $newArrThreshold;

            }
            else
            {
                $product->newArrival=null;
            }
            

            return $product;

        }

        public function setFormData($formData,$imageCount){

            $this->productname = $formData['productname'];
            $this->stock = $formData['stock'];
            $this->productcode = $formData['productcode'];
            $this->shortdecs = $formData['shortdecs'];
            $this->description = $formData['description'];
            $this->spec_neck = $formData['neck'];
            $this->spec_length = $formData['length'];
            $this->spec_occasion = $formData['occasion'];        
            
        }

        public function getImages(){
            return $this->images;
        }
    }

    Class ProductController extends ProductModel{

        private $conn;  

        public function __construct($db)
        {
            $this->conn = $db;
        }        

        public function getByOccasion($occassion){
            $query = "select productid,productcode,productname,stock,shortdecs,thumbnailImg from ".$this->tableName." where spec_occasion=? order by addedDate desc limit 0,2";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1,$occassion);

            $productsData = array();
            if($stmt->execute())
            {
                $rowCount = $stmt->rowCount();
                if($rowCount>0)
                {
                    while($productData = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        $product = ProductModel::fromDatabase($productData);
                        array_push($productsData, $product);
                    }
                }
            }
            return $productsData;
        }

        public function getNewArrivals($count = 3){
            $query = "select productid,addedDate,productcode,productname,stock,shortdecs,thumbnailImg from ".$this->tableName." order by addedDate desc limit 0,".$count;
            $stmt = $this->conn->prepare($query);            
            $productsData = array();
            if($stmt->execute())
            {
                $rowCount = $stmt->rowCount();
                if($rowCount>0)
                {
                    $newArrThreshold = new DateTime(date('Y-m-d', strtotime('-14 day', strtotime(date("Y-m-d")))));
                    while($productData = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        $product = ProductModel::fromDatabase($productData);
                        $productDate = new DateTime($product->addedDate);
                        if($productDate > $newArrThreshold)
                            array_push($productsData, $product);
                    }
                }
            }
            return $productsData;
        }
        
        public function uploadImagesFiles($images,$productid){
            $targetDir = $this->fileStorageRoot . $productid.'/';            
            $uploadOk = 1; 

            $currentFile=0;  
            $imagesArray =array();          
            foreach($images['name'] as $image){     
                $imageFileType = strtolower(pathinfo($image,PATHINFO_EXTENSION));
                $targetFile =  $targetDir."img_".$currentFile.".".$imageFileType;
                
                $check = getimagesize($images['tmp_name'][$currentFile]);
                array_push($imagesArray,$targetFile);

                if($check ==false){
                    $uploadOk = 0;
                }

                if($uploadOk == 0)
                {
                    break;
                }

                if(!is_dir($targetDir)) {
                    mkdir($targetDir);
                }

                if(move_uploaded_file($images['tmp_name'][$currentFile],$targetFile))
                {
                    $uploadOk=1;                
                }

                $currentFile++;
                
            }

            if($uploadOk == 1)
            {
                return json_encode($imagesArray);
            }

            return false;
                                    
        }


        public function UpdateImages($thumbnail,$images,$lastId)
        {
            $query = "update ".$this->tableName." SET 
                      thumbnailImg= :thumbnail,
                      images= :images
                      where productid=:id";
            
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":thumbnail", $thumbnail);
            $stmt->bindParam(":images", $images);
            $stmt->bindParam(":id", $lastId);


            if($stmt->execute())
            {
                $stmt->closeCursor();
                return true;
            }
            return false;

        }

        public function uploadThumbnail($thumbnail,$productid){
            $targetDir = $this->fileStorageRoot . $productid.'/';
            $imageFileType = strtolower(pathinfo($thumbnail['name'],PATHINFO_EXTENSION));
            $targetFile =  $targetDir. "thumbnail." . $imageFileType;
            $uploadOk = 1; 
            

            $check = getimagesize($thumbnail['tmp_name']);
            if($check ==false){
                $uploadOk = 0;
            }

            if($uploadOk == 0)
            {
                return false;
            }

            if(!is_dir($targetDir)) {
                mkdir($targetDir);
            }

            if( move_uploaded_file($thumbnail['tmp_name'],$targetFile))
            {
                $uploadOk=1;
                return $targetFile;
            }

            return false;
            
        }
        public function getProductById($id){

            $query = "select * from ".$this->tableName." where productid = ?";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1,$id);
            
            $productData = null;
            if($stmt->execute())
            {
                $rowCount = $stmt->rowCount();
                if($rowCount>0)
                {
                    $productData = $stmt->fetch(PDO::FETCH_ASSOC);
                    $productData = ProductModel::fromDatabase($productData);
                }

                $stmt->closeCursor();                
            }

            return $productData;
        }
        
        public function deleteProduct($productId)
        {
            $query = "delete from ".$this->tableName." where productid=?";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $productId);

            if($stmt->execute())
            {
                $stmt->closeCursor();
                return $stmt->rowCount();             
            }

            return false;
        }

        public function updateProductStock($productId,$stock)
        {
            
            $query = "update ".$this->tableName." set stock=? where productid=?";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $stock);
            $stmt->bindParam(2, $productId);

            if($stmt->execute())
            {
                $stmt->closeCursor();
                return $stmt->rowCount();              
            }

            return false;

        }

        public function getAllProducts($fromId = 0)
        {
            $query = "select productid,addedDate,productcode,productname,stock,shortdecs,thumbnailImg from ".$this->tableName." where productid > ?";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(1,$fromId);
            
            if($stmt->execute())
            {
                $rowCount = $stmt->rowCount();
                if($rowCount>0)
                {
                    $productList = [];
                    while($product = $stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        $productObj = ProductModel::fromDatabase($product);
                        array_push($productList,$productObj);
                    }

                    return $productList;
                }
            }

            return null;
        }

        public function createProduct($productData){
            $query = "insert into ".$this->tableName." 
                            SET
                               productname = :pname,
                               productCode = :pcode,
                               stock = :stock,
                               shortdecs = :shortdesc,
                               description = :desc,
                               spec_neck = :neck,
                               spec_length = :length,
                               spec_occasion = :occasion;                               
                     ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':pname',$this->productname);
            $stmt->bindParam(':pcode',$this->productcode);
            $stmt->bindParam(':stock',$this->stock);
            $stmt->bindParam(':shortdesc',$this->shortdecs);
            $stmt->bindParam(':desc',$this->description);
            $stmt->bindParam(':neck',$this->spec_neck);
            $stmt->bindParam(':length',$this->spec_length);
            $stmt->bindParam(':occasion',$this->spec_occasion);            

            if($stmt->execute())
            {
                $last_id = $this->conn->lastInsertId();
                $stmt->closeCursor();
                return $last_id;
            }
            $stmt->closeCursor(); //Close Cursor
            return false;

            

        }


    }

?>