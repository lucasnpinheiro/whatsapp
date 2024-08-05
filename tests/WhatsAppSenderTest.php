<?php

namespace LucasNPinheiro\Whatsapp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use LucasNPinheiro\Whatsapp\WhatsAppSender;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class WhatsAppSenderTest extends TestCase
{
    public function testSendTemplateMessage()
    {
        $token = 'test_token';
        $phoneNumberId = 'test_phone_number_id';
        $to = '5516992660128';
        $templateName = 'hello_world';
        $languageCode = 'en_US';

        $clientMock = $this->createMock(Client::class);
        $responseMock = new Response(200, [], 'Success');

        $clientMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("$phoneNumberId/messages"),
                $this->callback(function ($options) use ($token, $to, $templateName, $languageCode) {
                    $this->assertEquals('Bearer ' . $token, $options['headers']['Authorization']);
                    $this->assertEquals('application/json', $options['headers']['Content-Type']);
                    $this->assertEquals($to, $options['json']['to']);
                    $this->assertEquals($templateName, $options['json']['template']['name']);
                    $this->assertEquals($languageCode, $options['json']['template']['language']['code']);
                    return true;
                })
            )
            ->willReturn($responseMock);

        $whatsappSender = new WhatsAppSender($token, $phoneNumberId);

        // Use reflection to set the $client private property
        $reflection = new ReflectionClass($whatsappSender);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($whatsappSender, $clientMock);

        $response = $whatsappSender->sendTemplateMessage($to, $templateName, $languageCode);

        $this->assertEquals('Success', $response);
    }

    public function testSendBoletoMessage()
    {
        $token = 'test_token';
        $phoneNumberId = 'test_phone_number_id';
        $to = '5516992660128';
        $documentUrl = 'https://example.com/document.pdf';
        $filename = 'document.pdf';
        $caption = 'Aqui está o seu boleto.';

        $clientMock = $this->createMock(Client::class);
        $responseMock = new Response(200, [], 'Success');

        $clientMock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo("$phoneNumberId/messages"),
                $this->callback(function ($options) use ($token, $to, $documentUrl, $filename, $caption) {
                    $this->assertEquals('Bearer ' . $token, $options['headers']['Authorization']);
                    $this->assertEquals('application/json', $options['headers']['Content-Type']);
                    $this->assertEquals($to, $options['json']['to']);
                    $this->assertEquals($documentUrl, $options['json']['document']['link']);
                    $this->assertEquals($filename, $options['json']['document']['filename']);
                    $this->assertEquals($caption, $options['json']['document']['caption']);
                    return true;
                })
            )
            ->willReturn($responseMock);

        $whatsappSender = new WhatsAppSender($token, $phoneNumberId);

        // Use reflexão para definir a propriedade privada $client
        $reflection = new ReflectionClass($whatsappSender);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($whatsappSender, $clientMock);

        $response = $whatsappSender->sendDocument($to, $documentUrl, $filename, $caption);

        $this->assertEquals('Success', $response);
    }
}