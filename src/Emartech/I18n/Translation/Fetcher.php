<?php


namespace Emartech\I18n\Translation;

use Exception;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client as HttpClient;

class Fetcher
{
    /**
     * @var array
     */
    private $endPointUrls;

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $translations = [];


    public function __construct(array $url, HttpClient $client, LoggerInterface $logger)
    {
        $this->endPointUrls = $url;
        $this->client = $client;
        $this->logger = $logger;
    }

    public function getTranslations(string $lang): array
    {
        if (!array_key_exists($lang, $this->translations)) {
            $this->translations[$lang] = [];
            foreach ($this->endPointUrls as $url) {
                $this->translations[$lang] = array_merge($this->translations[$lang], $this->collectTranslations($url, $lang));
            }
        }

        return $this->translations[$lang];
    }

    private function collectTranslations(string $url, string $lang): array
    {
        try {
            return $this->process($this->fetchTranslations($url, $lang));
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

    private function fetchTranslations(string $url, string $lang): string
    {
        $result = (string)$this->client->request('GET', $url, ['query' => ['lang' => $lang]])->getBody();
        if (!$result) {
            throw new Exception('Response was empty');
        }
        return $result;
    }

    private function process($json): array
    {
        $result = json_decode($json, true);
        if (!is_array($result)) {
            throw new Exception('Invalid response');
        }
        return $result;
    }
}
