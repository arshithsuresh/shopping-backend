<?php

header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../config/core.php';
include_once '../models/user.php';

include_once '../libs/jwt-auth/BeforeValidException.php';
include_once '../libs/jwt-auth/ExpiredException.php';
include_once '../libs/jwt-auth/SignatureInvalidException.php';
include_once '../libs/jwt-auth/JWT.php';
include_once '../libs/jwt-auth/TokenManager.php';

use Firebase\JWT\ExpiredException;
use \Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;

function ValidateUser($accessToken){

    global $ACCESS_TOKEN_KEY;
    
    if($accessToken)
    {
        try{

            $decodedJWT = JWT::decode($accessToken,$ACCESS_TOKEN_KEY,array('HS256'));

            $AccessToken = new AccessToken();  

            http_response_code(200);
            
            echo json_encode(array(
                "message" => "Access Granted",
                "data" => $decodedJWT->data
            ));
        }
        catch(ExpiredException $expired)
        {
            http_response_code(401);            
            echo json_encode(array(
                "error" => "Authentication Failed",
                "message" => "The access token has expired"
            ));
        }
        catch(SignatureInvalidException $invalidSign)
        {
            http_response_code(401);            
            echo json_encode(array(
                "error" => "Authentication Failed",
                "message" => "Signatuer is Invalid"
            ));
        }
        
    }
    else
    {
        http_response_code(401);

        echo json_encode(array(
                "message" => "No Access Token",
                "error" => "Access Denied"));
    }

}

?>