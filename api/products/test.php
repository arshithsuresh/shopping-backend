<?php

    header("Access-Control-Allow-Origin: http://localhost/bikeRentalApi/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    include '../models/product.php';
    include_once '../config/database.php';
    include_once '../config/core.php';


    $database = new Database();
    $db = $database->getConnection();

    $productHandle = new Product($db);

    $productData = $productHandle->getProductById("111");
    echo json_encode($productData);
    
?>