<?php

namespace App;

use Curl\Curl;

class HttpRequest
{
    private $client;

    public function __construct()
    {
        $this->client = new Curl();
        $this->client->setHeader('User-Agent', $this->generateUserAgent());
        $this->client->setHeader('sec-ch-ua', $this->generateUniqueSecChUa());
    }

    public function getClient()
    {
        srand(time());
        return $this->client;
    }

    private function generateUserAgent()
    {
        $operatingSystems = [
            'Windows NT 10.0',
            'Windows NT 6.1',
            'Macintosh',
            'Linux; Android 6.0',
            'iPhone; CPU iPhone OS 14_0',
        ];

        $browsers = [
            'Chrome/118.0.0.0',
            'Firefox/100.0',
            'Safari/537.36',
            'Edge/100.0',
            'Opera/80.0',
        ];

        $platforms = [
            'Win64',
            'Win32',
            'MacIntel',
            'Linux x86_64',
            'iPhone',
        ];

        $os = $operatingSystems[array_rand($operatingSystems)];
        $browser = $browsers[array_rand($browsers)];
        $platform = $platforms[array_rand($platforms)];

        return "Mozilla/5.0 ($os) AppleWebKit/537.36 (KHTML, like Gecko) $browser $platform";
    }

    private function generateUniqueSecChUa()
    {
        $browsers = [
            '"Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            '"Firefox";v="100", "Mozilla";v="5.0"',
            '"Edge";v="100", "Spartan";v="5.0"',
            '"Opera";v="70", "Presto";v="2.1"',
            '"Safari";v="14.0", "Macintosh";v="Intel Mac OS X 10_15"',
        ];
        return $browsers[array_rand($browsers)];
    }

    public function __destruct()
    {
        $this->client->close();
    }

}
