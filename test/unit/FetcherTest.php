<?php

use Emartech\Lang\Fetcher;
use Emartech\TestHelper\BaseTestCase;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Client as HttpClient;

class FetcherTest extends BaseTestCase
{
    /**
     * @var HttpClient|PHPUnit_Framework_MockObject_MockObject
     */
    private $clientMock;

    /**
     * @var LoggerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var Fetcher
     */
    private $subject;


    public function setUp()
    {
        parent::setUp();
        $this->loggerMock = $this->mock(LoggerInterface::class);
        $this->clientMock = $this->mock(HttpClient::class);
        $this->subject = new Fetcher('', $this->clientMock, $this->loggerMock);
    }


    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_EmptyArrayReturned()
    {
        $this->expectHttpRequestToFail();
        $this->assertEquals([], $this->subject->getTranslations('en'));
    }


    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_ErrorLogged()
    {
        $this->expectHttpRequestToFail();
        $this->expectRequestFailureToBeLogged();
        $this->subject->getTranslations('en');
    }


    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_TryDownloadOnlyOnce()
    {
        $this->expectHttpRequestToFail();
        $this->subject->getTranslations('en');
        $this->subject->getTranslations('en');
    }


    /**
     * @test
     */
    public function getTranslations_TranslationsFound_ReturnProperTranslationsArray()
    {
        $this->expectTranslationsReturned();
        $expectedTranslationsArray = [
                'translation 1' => 'translation one',
                'translation 2' => 'translation two'
        ];

        $this->assertEquals($expectedTranslationsArray, $this->subject->getTranslations('en'));
    }


    private function expectHttpRequestToFail()
    {
        /** @var RequestException|PHPUnit_Framework_MockObject_MockObject $exception */
        $exception = $this->mock(RequestException::class);
        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->will($this->throwException($exception));
    }


    private function expectRequestFailureToBeLogged()
    {
        $this->loggerMock->expects($this->once())->method('error');
    }


    public function expectTranslationsReturned()
    {
        $translationResponseBody = '{"translation 1":"translation one","translation 2":"translation two"}';
        $response = $this->mock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($translationResponseBody);

        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($response);
    }
}
