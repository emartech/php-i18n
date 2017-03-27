<?php

use Emartech\I18n\Translation\Fetcher;
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
    private $fetcher;


    public function setUp()
    {
        parent::setUp();
        $this->loggerMock = $this->mock(LoggerInterface::class);
        $this->clientMock = $this->mock(HttpClient::class);
        $this->fetcher = new Fetcher([''], $this->clientMock, $this->loggerMock);
    }

    /**
     * @test
     */
    public function getTranslations_MultipleUrlsPassed_BothLoaded()
    {
        $this->clientMock->expects($this->at(0))->method('request')->willReturn($this->mockResponse('{"trans 1": "ford 1"}'));
        $this->clientMock->expects($this->at(1))->method('request')->willReturn($this->mockResponse('{"trans 2": "ford 2"}'));

        $fetcher = new Fetcher(['url1', 'url2'], $this->clientMock, $this->loggerMock);
        $this->assertEquals(['trans 1' => 'ford 1', 'trans 2' => 'ford 2'], $fetcher->getTranslations('en'));
    }

    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_EmptyArrayReturned()
    {
        $this->expectHttpRequestToFail();
        $this->assertEquals([], $this->fetcher->getTranslations('en'));
    }

    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_ErrorLogged()
    {
        $this->expectHttpRequestToFail();
        $this->expectRequestFailureToBeLogged();
        $this->fetcher->getTranslations('en');
    }

    /**
     * @test
     */
    public function getTranslations_NoTranslationsAvailable_TryDownloadOnlyOnce()
    {
        $this->expectHttpRequestToFail();
        $this->fetcher->getTranslations('en');
        $this->fetcher->getTranslations('en');
    }

    /**
     * @test
     */
    public function getTranslations_TranslationsFound_ReturnProperTranslationsArray()
    {
        $this->expectTranslationsReturned('en');
        $expectedTranslationsArray = [
                'translation 1' => 'translation one en',
                'translation 2' => 'translation two en'
        ];

        $this->assertEquals($expectedTranslationsArray, $this->fetcher->getTranslations('en'));
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

    public function expectTranslationsReturned($lang)
    {
        $translationResponseBody = '{"translation 1":"translation one '.$lang.'","translation 2":"translation two '.$lang.'"}';
        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->mockResponse($translationResponseBody));
    }

    private function mockResponse(string $translationResponseBody): PHPUnit_Framework_MockObject_MockObject
    {
        $response = $this->mock(ResponseInterface::class);
        $response->expects($this->once())->method('getBody')->willReturn($translationResponseBody);
        return $response;
    }
}
