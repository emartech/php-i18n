<?php

namespace {
    if (!function_exists('t')) {
        function t(string $text): string {
            return $GLOBALS['translator']->translate($text);
        }
    }
}

namespace Emartech\Lang
{
    use Psr\Log\LoggerInterface;
    use GuzzleHttp\Client as HttpClient;
    use GuzzleHttp\Exception\RequestException;

    class Fetcher
    {
        /**
         * @var string
         */
        private $url;

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


        public function __construct(string $url, HttpClient $client, LoggerInterface $logger)
        {
            $this->url = $url;
            $this->client = $client;
            $this->logger = $logger;
        }

        public function initialize(string $lang)
        {
            $GLOBALS['translator'] = $this->createTranslator($lang);
        }

        public function createTranslator(string $lang): Translator
        {
            return new Translator($this->getTranslations($lang), $this->logger);
        }

        public function getTranslations(string $lang): array
        {
            if (!array_key_exists($lang, $this->translations)) {
                $this->fetchTranslations($lang);
            }

            return $this->translations[$lang];
        }


        private function fetchTranslations(string $lang)
        {
            $json = null;

            try {
                $response = $this->client->request('GET', $this->url, ['query' => ['lang' => $lang]]);

                $json = (string)$response->getBody();
                unset($response);
            } catch (RequestException $e) {
                $this->logger->error($e->getMessage());
            }

            $translationsForLang = [];
            if ($json) {
                $decoded = json_decode($json, true);
                if (is_array($decoded)) {
                    $translationsForLang = $decoded;
                }
            }

            $this->translations[$lang] = $translationsForLang;
        }
    }
}

