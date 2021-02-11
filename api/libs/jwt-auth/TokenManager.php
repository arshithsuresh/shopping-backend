<?php

date_default_timezone_set('Asia/Kolkata');

//JWT Files
include_once 'BeforeValidException.php';
include_once 'ExpiredException.php';
include_once 'SignatureInvalidException.php';
include_once 'JWT.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;


$ACCESS_TOKEN_KEY = "hxhgHGAS6786SAxz87S@23hkas8976";
$REFRESH_TOKEN_KEY = "abdg6hhGGGYT34Ako098HJjhuy77hfsjh324h";

class Token
{
    public $iat;    
    public $iss = "http://localhost/topzone";

    public $tokenStatus = true;


    function __construct()
    {
        $this->iat = time();        
    }   

    public function createToken($key)
    {
        $dataToEncode = array   ("iss"=>$this->iss,
                        "exp"=>$this->exp,
                        "iat"=>$this->iat,  
                        "data"=>$this->data                      
                    );

        return JWT::encode($dataToEncode,$key);
    }

    function getTokenStatus(){        

        return $this->tokenStatus;
    }

    function setTokenStatus($status){
        $this->tokenStatus = $status;
    }
    
}

class AccessToken extends Token
{    
    public $exp;    
    public $data;
    public $expireTime = 25;

    public function __construct()
    {
        // Call Parent Constructor
        parent::__construct();
    }

    public static function withBody ($dataBody)
    {   
        $instance = new self();

        $instance->data = $dataBody;       
        $instance->exp = $instance->iat + $instance->expireTime;             
        
        return $instance;
    }    

    public static function Decode($tokenString,$key)
    {
        
    }    

    public function decodeToken($tokenString,$key)
    {
        $tokenDecoded = NULL;

        try
        {
            $tokenDecoded = JWT::decode($tokenString,$key,array('HS256'));
            
        }   
        catch(ExpiredException $expired)
        {
            $this->setTokenStatus(false);           
        }
        catch(SignatureInvalidException $invalidSign)
        {
            $this->setTokenStatus(false);
        }
        if($this->getTokenStatus())
            return $tokenDecoded;

        return null;
    } 
    
}

Class RefreshToken extends Token
{
    public $exp;    
    public $data;
    public $expireTime = 60*60;

    public function __construct()
    {
        // Call Parent Constructor
        parent::__construct();
    }

    public static function createNew ()
    {   
        $instance = new self();           
        $instance->exp = $instance->iat + $instance->expireTime; 
        
        return $instance;
    }      

    public function decodeToken($tokenString,$key)
    {
        $tokenDecoded = NULL;

        try
        {
            $tokenDecoded = JWT::decode($tokenString,$key,array('HS256'));
            
        }   
        catch(ExpiredException $expired)
        {
            $this->setTokenStatus(false);           
        }
        catch(SignatureInvalidException $invalidSign)
        {
            $this->setTokenStatus(false);
        }
        if($this->getTokenStatus())
            return $tokenDecoded;

        return null;
    }    

    public function toJSON()
    {
        
    }
    
}


?>