<?php

function curlExec($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, 'X');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

$data = json_decode(curlExec('https://api.github.com/repos/maxime-gaudron/php-deployment-tool/branches'), true);

$formattedData = array();
foreach($data as $branch) {
    $formattedData[$branch['name']] = $branch['name'];
}

echo json_encode($formattedData);
