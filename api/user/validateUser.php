<?php
include_once './functions.php';

$accessToken = isset($_SERVER['HTTP_ACCESSTOKEN'])?$_SERVER['HTTP_ACCESSTOKEN']:die("{'error':'No Access Token'}");
ValidateUser($accessToken);

http_response_code(200);
        
echo json_encode(array(
    "message" => "Access Granted",
    "data" => $decodedJWT->data
));



?>