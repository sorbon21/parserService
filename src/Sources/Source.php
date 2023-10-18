<?php

namespace App\Sources;

abstract class Source
{
    private $inputData;
    private $outputData;
    private $outputCode;
    const SUCCESS_STATUS_CODE = 1;
    const ERROR_STATUS_CODE = 0;

    abstract public function run();

    public function setInputData(array $inputData)
    {
        $this->inputData = $inputData;
    }

    protected function getInputData()
    {
        return $this->inputData;
    }

    public function getOutputData()
    {
        return $this->outputData;
    }

    protected function setOutputCode($code)
    {
        $this->outputCode = $code;
    }

    public function getOutputCode()
    {
        return $this->outputCode;
    }

    protected function setOutputData(array $data)
    {
        $this->outputData = $data;
    }
}
