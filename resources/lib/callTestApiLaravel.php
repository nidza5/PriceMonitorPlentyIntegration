<?php
 
$client = new \GuzzleHttp\Client();
$res = $client->request(
    'GET',
    '127.0.0.1:8012/articles',
    [
    ]
);
 
/** @return array */
return json_decode($res->getBody(), true);