<?php

$url = 'https://pixelnos-ledger-api.herokuapp.com/BackEnd/index.php?action=connection/SignUp';
$_GET["action"]
$data = $_POST;
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
print_r($result);
