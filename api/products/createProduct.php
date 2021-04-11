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
    
    $files = $_FILES;  
    $formData = $_POST;
    $database = new Database();
    $db = $database->getConnection();

    $productHandle = new ProductController($db);

    $imageCount = count($file['images']['name']);

    $productHandle->setFormData($formData,$imageCount); 
    $productHandle->uploadThumbnail($files['thumbnail'],125);
    $productHandle->uploadImagesFiles($files['images'],125);
    
    print_r($formData);

?>