<?php

namespace Whatsapp\Tests;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Whatsapp\WhatsAppTemplateSender;

class WhatsAppTemplateSenderTest extends TestCase
{
    private $mockHandler;
    private $client;
    private $whatsAppTemplateSender;
    private $validToken = 'valid_access_token';
    private $validBusinessPhoneNumberId = 'valid_business_phone_number_id';

    public function testHelloWorldSuccess()
    {
        // Configurar MockHandler para retornar uma resposta de sucesso
        $this->mockHandler->append(new Response(200, [], json_encode([
            'messages' => [
                ['id' => 'wamid.HBgLNzc4OTg1NTU2OTU1FQIAERgSMSswMDIwMDE4ODk5MTg2MzE5MjM2NTAA']
            ]
        ])));

        // Chama o método helloWorld
        $response = $this->whatsAppTemplateSender->helloWorld('5516992660128');

        // Verifica se a resposta contém o ID da mensagem
        $this->assertArrayHasKey('messages', $response);
        $this->assertEquals(
            'wamid.HBgLNzc4OTg1NTU2OTU1FQIAERgSMSswMDIwMDE4ODk5MTg2MzE5MjM2NTAA',
            $response['messages'][0]['id']
        );
    }

    public function testBuildTextHeaderComponent()
    {
        $component = $this->whatsAppTemplateSender->buildTextHeaderComponent('Hello World');

        $expected = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => 'Hello World'
                ]
            ]
        ];

        $this->assertEquals($expected, $component);
    }

    public function testBuildDocumentHeaderComponent()
    {
        $component = $this->whatsAppTemplateSender->buildDocumentHeaderComponent('document-id', 'example.pdf');

        $expected = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'document',
                    'document' => [
                        'id' => 'document-id',
                        'filename' => 'example.pdf'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $component);
    }

    public function testBuildDocumentHeaderComponentByLink()
    {
        $component = $this->whatsAppTemplateSender->buildDocumentHeaderComponentByLink(
            'http://example.com/document.pdf',
            'provider-name',
            'example.pdf'
        );

        $expected = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'document',
                    'document' => [
                        'link' => 'http://example.com/document.pdf',
                        'provider' => ['name' => 'provider-name'],
                        'filename' => 'example.pdf'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $component);
    }

    public function testSendTemplateMessageSuccess()
    {
        // Configurar MockHandler para retornar uma resposta de sucesso
        $this->mockHandler->append(new Response(200, [], json_encode([
            'messages' => [
                ['id' => 'wamid.HBgLNzc4OTg1NTU2OTU1FQIAERgSMSswMDIwMDE4ODk5MTg2MzE5MjM2NTAA']
            ]
        ])));

        $components = [$this->whatsAppTemplateSender->buildTextHeaderComponent('Hello World')];

        // Chama o método sendTemplateMessage
        $response = $this->whatsAppTemplateSender->sendTemplateMessage(
            '5516992660128',
            'template_name',
            'en_US',
            $components
        );

        // Verifica se a resposta contém o ID da mensagem
        $this->assertArrayHasKey('messages', $response);
        $this->assertEquals(
            'wamid.HBgLNzc4OTg1NTU2OTU1FQIAERgSMSswMDIwMDE4ODk5MTg2MzE5MjM2NTAA',
            $response['messages'][0]['id']
        );
    }

    public function testHelloWorldFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageContains('Erro ao enviar a mensagem: Client error:');

        $sender = new WhatsAppTemplateSender('invalid_access_token', 'invalid_business_phone_number_id');
        $sender->helloWorld('invalid_recipient_id');
    }

    private function expectExceptionMessageContains(string $expectedSubstring)
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedSubstring);
    }

    public function testSendTemplateMessageFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageContains('Erro ao enviar a mensagem: Client error:');

        $sender = new WhatsAppTemplateSender('invalid_access_token', 'invalid_business_phone_number_id');
        $components = []; // ou os componentes que você deseja enviar
        $sender->sendTemplateMessage('valid_recipient_id', 'invalid_template_name', 'en_US', $components);
    }

    // Helper method to check part of the exception message

    protected function setUp(): void
    {
        // Configurando MockHandler para simular as respostas da API
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->client = new Client(['handler' => $handlerStack]);

        // Instancia a classe WhatsAppTemplateSender com um cliente mockado
        $this->whatsAppTemplateSender = new WhatsAppTemplateSender(
            'fake-access-token', 'fake-business-phone-number-id'
        );
        $this->whatsAppTemplateSender->client = $this->client;
    }
}