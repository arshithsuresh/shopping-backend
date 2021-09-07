<?php
include_once './functions.php';



if(isset($_SERVER['HTTP_ACCESSTOKEN']))
{
    echo "Token is sett";
}
else
    echo "Token is not sett";

$accessToken = isset($_SERVER['HTTP_ACCESSTOKEN'])?$_SERVER['HTTP_ACCESSTOKEN']:die("{'error':'No Access Token'}");
$validation = ValidateUser($accessToken);

if($validation)
{
    http_response_code(200);
    $data = DecodeToken($accessToken);
    echo json_encode(array(
        "message" => "Access Granted",
        "data" => $data->data
    ));
}

// http_response_code(200);
        
// echo json_encode(array(
//     "message" => "Access Granted",
//     "data" => $decodedJWT->data
// ));



?>