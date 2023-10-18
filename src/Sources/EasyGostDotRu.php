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
        $curl = $this->httpRequest;
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
            $keys = [
                "VIN" => 'vin',
                "Дата" => 'date',
                "Место" => 'company',
                "Работы" => 'work',
                "Организатор отзывной кампании" => 'organizer_of_the_recall_campaign',
                "Марка" => 'make',
                "Дата начала отзывной кампании" => 'date_bigin_the_recall_campaign',
                "Коммерческое название ТС" => 'commercial_name_ts',
                "Причины отзыва" => 'reason'
            ];
            $text = $node->text();

            $results = [];
            $key_names = array_keys($keys);

            foreach ($key_names as $key) {
                $pattern = "/$key:\s*(.*?)(?=\s*$|" . implode('|', array_map('preg_quote', $key_names)) . ")/us";
                preg_match($pattern, $text, $matches);
                if (!empty($matches[1])) {
                    $results[$keys[$key]] = trim($matches[1]);
                } else {
                    if ($key == "VIN") {
                        $results[$keys[$key]] = $this->getInputData()['vin'];

                    } else {
                        $results[$keys[$key]] = null;
                    }

                }
            }
            $result[] = $results;
        });


        return $result;

    }

}
