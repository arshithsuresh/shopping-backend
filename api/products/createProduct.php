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

    $imageCount = count($files['images']['name']);

    $productHandle->setFormData($formData,$imageCount); 
    $lastID=$productHandle->createProduct($formData);

    if($lastID!=false){ 

        $thumb=$productHandle->uploadThumbnail($files['thumbnail'],$lastID);
        $imgs=$productHandle->uploadImagesFiles($files['images'],$lastID);

        if( $thumb != false && $imgs!=false &&
            $productHandle->UpdateImages($thumb,$imgs,$lastID)){

                http_response_code(200);
                echo "{\"message\":\"Product created successfully!\"}";

        }
    }
    else
    {
        echo "{\"message\":\"Product creation failed. Try Again!\"}";
        http_response_code(400);
    }

    
    
    //print_r($formData);

?>