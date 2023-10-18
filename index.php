<?php
require_once 'vendor/autoload.php';

$vin = new \App\Services\VinInfo();
$vin->setInputData(['vin' => 'RUMKE1978EV021058']);
$vin->run();
print_r($vin->getOutputData());
print_r($vin->getOutputCode());
