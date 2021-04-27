<?php

    

    error_reporting(E_ALL);

    date_default_timezone_set('Asia/Kolkata');
    

    function sentResponseMessage($message)
    {
        echo "{\"message\": \"".$message."\"}";
    }

    function sentMessage($message=NULL,$error=NULL)
    {
        if($error == NULL && $message == NULL)
            echo json_encode(array("error"=>'0',"message"=>"Unidentified Error Occured!"));
        if(!empty($message) && empty($error))
            echo json_encode(array("error"=>'0',"message"=>$message));
        if(!empty($error))
        {
            switch($error)
            {
                case 1062:
                    echo json_encode(array("error"=>'1062',"message"=>"Email or Number already in use!"));
                break;
                default:
                    echo json_encode(array("error"=>'404',"message"=>$message));
                break;
            }
        }
    }

    

?>