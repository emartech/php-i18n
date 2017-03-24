<?php

namespace Emartech\Lang;

use Psr\Log\LoggerInterface;

class Translator
{
    private $translations;
    private $logger;
    private static $translator;

    public static function createInstance($lang, Fetcher $fetcher, LoggerInterface $logger): Translator
    {
        if (self::$translator === null) {
            self::$translator = new Translator($fetcher->getTranslations($lang), $logger);
        }

        return self::$translator;
    }

    public static function getInstance(): Translator
    {
        return self::$translator;
    }

    public function __construct(array $translations, LoggerInterface $logger)
    {
        $this->translations = $translations;
        $this->logger = $logger;
    }

    public function translate(string $text) : string
    {
        $ret = $text;

        if (array_key_exists($text, $this->translations)) {
            $ret = $this->translations[$text];
        } else {
            $this->logger->info("Missing translation for text: [ $text ]");
        }

        return $ret;
    }
}
