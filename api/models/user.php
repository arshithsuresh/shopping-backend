<?php
    class ClientUser
    {
        private $conn;
        private $tableName = "user_data";

        public $id;
        public $username;
        public $email;
        public $password;
        public $fName;
        public $lName;
        public $location;

        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function setUsername($username)
        {
            $this->username = $username;
        }

        public function setEmail($email)
        {
            $this->email = $email;
        }

        public function setUserDetails($uname, $em, $pass,$fn,$ln,$loc)
        {
            $this->username = $uname;
            $this->email =$em;
            $this->password = $pass;
            $this->fName = $fn;
            $this->lName = $ln;
            $this->location = $loc;
        }

        public function getRefreshTokenPassword($username){
            $query = "SELECT username,email,location,fName,lName,password,refreshToken from ".$this->tableName." where username= ?";
            $stmt = $this->conn->prepare($query);

            $username=htmlspecialchars(strip_tags($username));

            $stmt->bindParam(1,$username);
            $stmt->execute();

            $rowCount=$stmt->rowCount();
            $row=null;
            if($rowCount>0)
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
               
            }
            $stmt->closeCursor();
                
            return $row;         
        }
        //Check if user exits and return the details
        public function checkUserExits()
        {
            $query = "SELECT id,email,location,fName,lName,password FROM ". $this->tableName . " WHERE username = ? LIMIT 0,1";
            
            $stmt = $this->conn->prepare($query);

            $this->email=htmlspecialchars(strip_tags($this->username));
            
            $stmt->bindParam(1,$this->username);
            $stmt->execute();

            $rowCount=$stmt->rowCount();
            
            if($rowCount>0)
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->id = $row['id'];
                $this->setUserDetails($this->username,$row['email'],$row['password'],$row['fName'],$row['lName'],$row['location']);                

                return true;
            }

            return false;

        }

        public function UpdateRefreshToken($tokenValue){

            $query = "UPDATE ".$this->tableName."
                        SET refreshToken= :tokenVal
                        WHERE username = :username
                    ";

            $stmt = $this->conn->prepare($query);
            $this->username=htmlspecialchars(strip_tags($this->username));

            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':tokenVal', $tokenValue);  

            if($stmt->execute())
            {
                //print("Updated Refresh Token : ".$this->username);
            }
            return $stmt->errorInfo()[1];

        }
        //Check if all required fields are set
        public function checkAllFields()
        {
            $requiredField =!empty($this->username)&&!empty($this->email)&&
                            !empty($this->password)&&!empty($this->fName)&&
                            !empty($this->lName)&&!empty($this->location);

            
            return $requiredField;
        }

        public function checkEmailExits()
        {
            $query ="";
        }

        public function createUser()
        {
            

            $query = "INSERT INTO ".$this->tableName." 
                    SET 
                         username = :username,
                         email = :email,
                         password = :password,
                         fName = :fName,
                         lName = :lName,
                         location = :location;
                    ";
            
            $stmt = $this->conn->prepare($query);
            
            $this->username=htmlspecialchars(strip_tags($this->username));
            $this->email=htmlspecialchars(strip_tags($this->email));
            $this->password=htmlspecialchars(strip_tags($this->password));
            $this->fName=htmlspecialchars(strip_tags($this->fName));
            $this->lName=htmlspecialchars(strip_tags($this->lName));
            $this->location=htmlspecialchars(strip_tags($this->location));

            //hash password
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
           
            //bind all params
            $stmt->bindParam(':username',$this->username);
            $stmt->bindParam(':email',$this->email);
            $stmt->bindParam(':password',$password_hash);
            $stmt->bindParam(':fName',$this->fName);
            $stmt->bindParam(':lName',$this->lName);
            $stmt->bindParam(':location',$this->location);
            
            if($stmt->execute())
            {
               //User Created Succesfully
            }
            $stmt->closeCursor(); //Close Cursor
            return $stmt->errorInfo()[1];
        }
    }
?>