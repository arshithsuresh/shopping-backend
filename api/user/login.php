<?php

    header("Access-Control-Allow-Origin: http://localhost/bikeRentalApi/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    include_once '../config/database.php';
    include_once '../config/core.php';
    include_once '../models/user.php';
  
    include_once '../libs/jwt-auth/TokenManager.php';

    $database = new Database();
    $db = $database->getConnection();

    $user = new ClientUser($db);

    $data = json_decode(file_get_contents("php://input"));

    $user->setUsername($data->username);
    $user->setEmail($data->email);

    $emailExits = $user->checkUserExits();
   
    
    if($emailExits && password_verify($data->password, $user->password))
    {
        $tokenData = array(
            "id"=> $user->id,
            "firstname" => $user->fName,
            "lastname" => $user->lName,
            "email" => $user->email,
            "location" => $user->location
        );   
        
        $refreshTokenData = array(
            "id"=> $user->id,
            "email"=> $user->email
        );

        $AccessToken = AccessToken::withBody($tokenData);
        $RefreshToken = RefreshToken::createNew($refreshTokenData);
                
        http_response_code(200);

        $AccessTokenString = $AccessToken->createToken($ACCESS_TOKEN_KEY);
        $RefeshTokenString = $RefreshToken->createToken($REFRESH_TOKEN_KEY.$user->password);

        $user->UpdateRefreshToken($RefeshTokenString);

        echo json_encode(array(
            "message" => "Successfull Login!",
            "accessToken" => $AccessTokenString,
            "refeshToken" => $RefeshTokenString
            
        ));
    }
    else
    {
        http_response_code(401);
        echo json_encode(array("message" => "Login Failed"));
    }
?>