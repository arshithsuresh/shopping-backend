<?php

    include_once 'functions.php';
    
    checkAuth();

    function checkAuth(){
        $accessToken = isset($_SERVER['HTTP_ACCESSTOKEN'])?$_SERVER['HTTP_ACCESSTOKEN']:die("{'error':'No Access Token'}");
        ValidateUser($accessToken);   
    }

?>