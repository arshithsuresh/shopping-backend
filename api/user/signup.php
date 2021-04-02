<?php

    header("Access-Control-Allow-Origin: http://localhost/bikerentalapi/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Method: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once '../config/core.php';
    include_once '../config/database.php';
    include_once '../models/user.php';

    //Get Database Connection
    $database = new Database();
    $db = $database->getConnection();

    $user = new ClientUser($db);

    //Get and Set sent data
    $data = json_decode(file_get_contents("php://input"));    
    $user->setUserDetails($data->username,$data->email,$data->password,$data->fName,$data->lName,$data->location);
    
    $errorCode=200;
    
    if($user->checkAllFields() && ($errorCode=$user->createUser())=='')
    {        
        http_response_code(200);        
        sentMessage("User Created Succesfully!");
    }
    else
    {
        http_response_code(400);           
        sentMessage(NULL,$errorCode);
    }


?>