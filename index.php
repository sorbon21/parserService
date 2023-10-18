<?php

require_once 'vendor/autoload.php';

use App\Services\VinInfo;
use App\HttpRequest;

$http = HttpRequest::getInstance();
//$http->setProxy('165.227.44.211', 3128);
//$http->setProxyAuth();
//$http->setProxyType();
//$http->setProxyTunnel();

$vin = new VinInfo();
$vin->setInputData(['vin' => 'Z8TVUAFZFBM950273']);
$vin->run();
echo 'service status: ' . $vin->getOutputCode() . PHP_EOL;
print_r($vin->getOutputData());
