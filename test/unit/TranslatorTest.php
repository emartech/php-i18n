<?php

use Emartech\I18n\Translation\Translator;
use Psr\Log\LoggerInterface;
use Emartech\TestHelper\BaseTestCase;

class TranslatorTest extends BaseTestCase
{
    /**
     * @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var Translator
     */
    private $translator;


    public function setUp()
    {
        parent::setUp();
        $this->loggerMock = $this->mock(LoggerInterface::class);
        $this->translator = new Translator(['translated' => 'text'], $this->loggerMock);
    }

    /**
     * @test
     */
    public function translate_NoTranslationAvailable_OriginalTextReturned()
    {
        $subject = new Translator([], $this->loggerMock);
        $this->assertEquals('test', $subject->translate('test'));
    }

    /**
     * @test
     */
    public function translate_TranslationsAvailableButNotTranslated_OriginalTextReturned()
    {
        $this->assertEquals('test', $this->translator->translate('test'));
    }

    /**
     * @test
     */
    public function translate_TranslationsAvailableButNotTranslated_MissingTranslationLogged()
    {
        $this->loggerMock->expects($this->once())->method('debug');
        $this->translator->translate('test');
    }

    /**
     * @test
     */
    public function translate_TranslationsAvailableAndTranslated_TranslatedTextReturned()
    {
        $this->assertEquals('text', $this->translator->translate('translated'));
    }
}
