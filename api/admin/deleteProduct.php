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

    $formData = $_POST;
    $database = new Database();
    $db = $database->getConnection();

    $productHandle = new ProductController($db);

    $result = $productHandle->deleteProduct($formData['productid']);

    if($result > 0)
    {
        http_response_code(200);
        sentResponseMessage("Product Deleted Successfully!");
        die();
    }

    http_response_code(400);
    sentResponseMessage("Product Deletion failed!");
?>