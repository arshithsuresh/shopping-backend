<?php

    

    error_reporting(E_ALL);

    date_default_timezone_set('Asia/Kolkata');
    

    function sentMessage($message=NULL,$error=NULL)
    {
        if($error == NULL && $message == NULL)
            echo json_encode(array("error"=>'0',"message"=>"Unidentified Error Occured!"));
        else if(!empty($message))
            echo json_encode(array("error"=>'0',"message"=>$message));
        else if(!empty($error))
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