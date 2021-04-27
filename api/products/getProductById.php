<?php
    header("Access-Control-Allow-Origin: http://localhost/topzoneapi/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Method: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include '../models/product.php';
    include_once '../config/database.php';
    include_once '../config/core.php';
    include_once '../user/functions.php';

    //Middlewares
    //Checking for authorization
    //include_once '../user/requireAuth.php';

    //End Middlewares

    $database = new Database();
    $db = $database->getConnection();

    $productHandle = new ProductController($db);
    
    if(!isset($_GET['productId']))
    {
        echo "{\"error\":\"No product ID requested\"}";
        die();
    }

    $productId = $_GET['productId'];

    $productData = $productHandle->getProductById($productId);
    

    if($productData !=null) 
    {
        http_response_code(200);
        echo json_encode($productData);
    }
    else
    {
        http_response_code(404);
        echo "{\"error\":\"No product found with ID\"}";
    }

?>