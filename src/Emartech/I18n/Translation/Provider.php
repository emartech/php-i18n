<?php

namespace {

    use Emartech\I18n\Translation\Provider;

    if (!function_exists('t')) {
        function t(string $text): string
        {
            return Provider::getTranslator()->translate($text);
        }
    }
}

namespace Emartech\I18n\Translation {

    use GuzzleHttp\Client;
    use Psr\Log\LoggerInterface;

    class Provider
    {
        /**
         * @var Translator
         */
        private static $translator;

        public static function initialize(array $endpointUrls, string $lang, LoggerInterface $logger)
        {
            if (self::$translator) {
                $logger->debug("Translator is already initialized with language {$lang}");
                return;
            }

            self::$translator = self::createTranslator($endpointUrls, $lang, $logger);
        }

        public static function getTranslator(): Translator
        {
            if (!self::$translator) {
                throw new \Exception("Translator has not been initialized!");
            }

            return self::$translator;
        }

        private static function createTranslator(array $endpointUrls, string $lang, LoggerInterface $logger): Translator
        {
            return new Translator((new Fetcher($endpointUrls, new Client(), $logger))->getTranslations($lang), $logger);
        }
    }
}
