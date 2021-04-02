<?php
include_once './functions.php';

$data = json_decode(file_get_contents("php://input"));
$accessToken = isset($data->accessToken)?$data->accessToken : "";

ValidateUser($accessToken);

?>