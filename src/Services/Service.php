<?php

namespace App\Services;

use App\Sources\Source;

abstract class Service
{
    private $inputData;
    private $outputData;
    private $outputCode;
    const SUCCESS_STATUS_CODE = 1;
    const ERROR_STATUS_CODE = 0;
    const MAX_ATTEMPT_COUNT = 3;
    const SLEEP_SECOND_MIN = 3;
    const SLEEP_SECOND_MAX = 10;

    abstract public function run();

    public function __construct()
    {
        srand(time());
    }

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

    protected function wait()
    {
        sleep(rand(self::SLEEP_SECOND_MIN, self::SLEEP_SECOND_MAX));
    }

    /**
     *
     * @param Source[] $sources
     */
    public function runJob(array $sources)
    {
        for ($i = 0; $i < self::MAX_ATTEMPT_COUNT; $i++) {
            echo 'ATTEMPT ' . ($i + 1) . PHP_EOL;
            foreach ($sources as $source) {
                if ($source instanceof Source) {
                    $source->run();
                    if ($source->getOutputCode() === $source::SUCCESS_STATUS_CODE) {
                        $this->setOutputCode(self::SUCCESS_STATUS_CODE);
                        $this->setOutputData($source->getOutputData());
                        break 2;
                    } else {
                        $this->setOutputCode(self::ERROR_STATUS_CODE);
                        $this->setOutputData($source->getOutputData());
                    }
                    $this->wait();
                }
            }
        }
    }

}
