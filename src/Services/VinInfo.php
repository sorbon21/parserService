<?php

namespace App\Services;

use App\Sources\EasyGostDotRu;

class VinInfo extends Service
{
    public function run()
    {
        $source = new EasyGostDotRu();
        $source->setInputData($this->getInputData());
        $this->runJob($source);
    }
}
