<?php
    header("Access-Control-Allow-Origin: http://localhost/topzoneapi/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Method: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    include '../models/interestedList.php';
    include_once '../config/database.php';
    include_once '../config/core.php';
    include_once '../user/functions.php';

    $database = new Database();
    $db = $database->getConnection();

    $favoriteHandle = new InterestedList($db);

    if(!isset($_POST['userid']) || !isset($_POST['productid']) || !isset($_POST['status']))
    {
        http_response_code(401);
        print_r(json_encode(array('message'=>"Invalid Request.")));
        die();
    }
    
    $userId = $_POST['userid'];
    $productId = $_POST['produ1ctid'];
    $status = $_POST['status'];
    
    $result = $favoriteHandle->setStatus($userId,$productId,$message);   

    if($result == true)
    {
        http_response_code(200);
        echo json_encode(array('message'=>"Status has been changed."));
        die();
    }

    http_response_code(401);
    echo json_encode(array('message'=>"Operation Failed!"));

    
?>