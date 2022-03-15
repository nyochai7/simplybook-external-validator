<?php


include_once 'ExternalValidatorException.php';
include_once 'ExternalValidator.php';


$incomingData = json_decode(file_get_contents('php://input'),true);

//uncomment for local testing
/*
$incomingData = json_decode('{
    "service_id":"9",
    "provider_id":"1",
    "client_id":"8123",
    "client_email":"",
    "start_datetime":"2021-01-11 11:40:00",
    "end_datetime":"2021-01-11 11:45:00",
    "count":1,
    "additional_fields":[
        {
            "id":"ed8f5b7380f7111c592abf6f916fc2d0",
            "name":"Check number",
            "value":"112233445566"
        },
        {
            "id":"68700bfe1ba3d59441c9b14d4f94938b",
            "name":"Some string",
            "value":"simplybook"
        },
        {
            "id":"ac4c3775f20dcfdea531346ee5bc8ea4",
            "name":"Date of birth",
            "value":"1973-03-02"
        }
    ]
}',true);
*/
if(!$incomingData){
    echo json_encode(array());
} else {
    $validator = new ExternalValidator();
    // $result = $validator->validate($incomingData);
    echo json_encode (new stdClass);
}
