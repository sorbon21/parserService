<?php

namespace App\Sources;

use App\HttpRequest;
use DOMDocument;
use DOMXPath;
use Symfony\Component\DomCrawler\Crawler;

class EasyGostDotRu extends Source
{

    const SOURCE_URL = 'https://easy.gost.ru';

    public function run()
    {
        return $this->createRequest();
    }

    public function createRequest()
    {
        $http = new HttpRequest();
        $curl = $http->getClient();
        $curl->setHeader('Accept', '*/*');
        $curl->setHeader('Accept-Language', 'ru,en-US;q=0.9,en;q=0.8');
        $curl->setHeader('Connection', 'keep-alive');
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        $sessionCookie = 'session-cookie=' . substr(bin2hex(random_bytes(96)), 0, 96);
        $curl->setHeader('Cookie', $sessionCookie);
        $curl->setHeader('Origin', self::SOURCE_URL);
        $curl->setHeader('Referer', self::SOURCE_URL . '/');
        $curl->setHeader('Sec-Fetch-Dest', 'empty');
        $curl->setHeader('Sec-Fetch-Mode', 'cors');
        $curl->setHeader('Sec-Fetch-Site', 'same-origin');
        $curl->setHeader('X-Requested-With', 'XMLHttpRequest');
        $curl->setHeader('sec-ch-ua-mobile', '?1');
        $curl->setHeader('sec-ch-ua-platform', '"Android"');
        $curl->post(self::SOURCE_URL, $this->getInputData());
        if ($curl->error) {
            $this->setOutputCode(self::ERROR_STATUS_CODE);
            $this->setOutputData(['error' => ['code' => $curl->errorCode, 'messages' => $curl->errorMessage]]);
        } else {
            $response = $curl->response;

            $this->setOutputCode(self::SUCCESS_STATUS_CODE);
            $this->setOutputData($this->parseData($response));
        }
        return $this;
    }

    private function parseData($html)
    {
        $crawler = new Crawler($html);

        $result = [];
        $crawler->filter('.div-revocamp')->each(function (Crawler $node) use (&$result) {
            $text = $node->text();
            $keys = [
                "VIN",
                "Дата",
                "Место",
                "Дата начала отзывной кампании",
                "Организатор отзывной кампании",
                "Марка",
                "Коммерческое название ТС",
                "Причины отзыва"
            ];
            $results = [];
            foreach ($keys as $key) {
                $pattern = "/$key:\s*(.*?)(?=\s*$|" . implode('|', array_map('preg_quote', $keys)) . ")/us";
                preg_match($pattern, $text, $matches);
                if (!empty($matches[1])) {
                    $results[$key] = trim($matches[1]);
                } else {
                    $results[$key] = null;
                }
            }
            $result[] = $results;
        });


        return $result;

    }

}
