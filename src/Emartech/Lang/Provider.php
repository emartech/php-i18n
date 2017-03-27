<?php

namespace {

    use Emartech\Lang\Provider;

    if (!function_exists('t')) {
        function t(string $text): string
        {
            return Provider::getTranslator()->translate($text);
        }
    }
}

namespace Emartech\Lang {

    use GuzzleHttp\Client;
    use Psr\Log\LoggerInterface;

    class Provider
    {
        /**
         * @var Translator
         */
        private static $translator;

        public static function initialize(string $endpointUrl, string $lang, LoggerInterface $logger)
        {
            if (self::$translator) {
                $logger->debug("Translator is already initialized with language {$lang}");
                return;
            }

            self::$translator = self::createTranslator($endpointUrl, $lang, $logger);
        }

        public static function getTranslator(): Translator
        {
            if (!self::$translator) {
                throw new \Exception("Translator has not been initialized!");
            }

            return self::$translator;
        }

        private static function createTranslator(string $endpointUrl, string $lang, LoggerInterface $logger): Translator
        {
            return new Translator((new Fetcher($endpointUrl, new Client(), $logger))->getTranslations($lang), $logger);
        }
    }
}
