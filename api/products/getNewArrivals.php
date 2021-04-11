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

    //include_once '../user/requireAuth.php';

    $database = new Database();
    $db = $database->getConnection();

    $productHandle = new ProductController($db);

    $newArrivals = $productHandle->getNewArrivals();

    echo json_encode($newArrivals);

?>