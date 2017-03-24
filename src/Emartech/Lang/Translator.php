<?php

namespace Emartech\Lang;

use Psr\Log\LoggerInterface;

class Translator
{
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
