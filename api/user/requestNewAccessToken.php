<?php
    header("Access-Control-Allow-Origin: http://localhost/topzoneapi/");
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

    $data = json_decode(file_get_contents("php://input"));
    
    $refreshToken = isset($_SERVER['HTTP_REFRESHTOKEN'])?$_SERVER['HTTP_REFRESHTOKEN']:die("{'error':'No Access Token'}");
    
    $UUID = isset($data->username)?$data->username : "";

    if($refreshToken && $UUID)    {
        
        try{

            $database = new Database();
            $db = $database->getConnection();

            $user = new ClientUser($db);

            $userDetails = $user->getRefreshTokenPassword($UUID);
            $userPassword = $userDetails['password'];

            $user->username = $UUID;

            $currentValidRefresToken = $userDetails['refreshToken'];

            if($currentValidRefresToken == $refreshToken){
                
                $decodedRefreshToken = JWT::decode($refreshToken,$REFRESH_TOKEN_KEY.$userPassword,array('HS256'));

                $tokenData = array(
                    "username"=> $userDetails['username'],
                    "firstname" => $userDetails['fName'],
                    "lastname" => $userDetails['lName'],
                    "email" => $userDetails['email'],
                    "location" => $userDetails['location']
                );   
                
                $refreshTokenData = array(
                    "id"=> $userDetails['username'],
                    "email"=> $userDetails['email']
                );
                
                $AccessToken = AccessToken::withBody($tokenData);
                $RefreshToken = RefreshToken::createNew($refreshTokenData);

                $AccessTokenString = $AccessToken->createToken($ACCESS_TOKEN_KEY);
                $RefeshTokenString = $RefreshToken->createToken($REFRESH_TOKEN_KEY.$userDetails['password']);

                print_r($user->UpdateRefreshToken($RefeshTokenString));

                echo json_encode(array(
                    "message" => "New Tokens Created",
                    "accessToken" => $AccessTokenString,
                    "refeshToken" => $RefeshTokenString
                    
                ));

            }
            else
            {
                http_response_code(401);            
                echo json_encode(array(
                    "error" => "Authentication Failed",
                    "message" => "The Refresh Token Invalid",
                    "code" => "ERR42"
                ));
                die;
            }             
            


        }
        catch(ExpiredException $expired)
        {
            http_response_code(401);            
            echo json_encode(array(
                "error" => "Authentication Failed",
                "message" => "The Refresh Token has expired"
            ));
        }
        catch(SignatureInvalidException $invalidSign)
        {
            http_response_code(401);            
            echo json_encode(array(
                "error" => "AccessToken Creation Failed",
                "message" => "Signatuer is Invalid"
            ));
        }
        catch(Exception $e){
            http_response_code(401);            
            echo json_encode(array(
                "error" => "Authentication Failed",
                "message" => "The Refresh Token Invalid"
            ));
        }
    }
?>