<?php

declare(strict_types=1);

namespace Whatsapp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Class to send messages via WhatsApp API using the Guzzle library.
 */
class WhatsAppSender
{
    /**
     * Authentication token for the WhatsApp API.
     *
     * @var string
     */
    private $token;

    /**
     * ID of the phone number used to send messages.
     *
     * @var string
     */
    private $phoneNumberId;

    /**
     * Guzzle HTTP client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * WhatsAppSender class constructor.
     *
     * @param string $token Authentication token for the WhatsApp API.
     * @param string $phoneNumberId ID of the phone number used to send messages.
     */
    public function __construct($token, $phoneNumberId)
    {
        $this->token = $token;
        $this->phoneNumberId = $phoneNumberId;
        $this->client = new Client([
            'base_uri' => 'https://graph.facebook.com/v20.0/',
            'timeout' => 10.0,
        ]);
    }

    /**
     * Sends a template message to a specific number.
     *
     * @param string $to Recipient number in international format.
     * @param string $templateName Name of the template to be sent.
     * @param string $languageCode Template language code.
     * @return string API response.
     */
    public function sendTemplateMessage($to, $templateName, $languageCode)
    {
        $url = $this->phoneNumberId . '/messages';
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => [
                    "code" => $languageCode
                ]
            ]
        ];

        return $this->sendRequest($url, $data);
    }

    /**
     * Sends a POST request to the WhatsApp API and handles exceptions.
     *
     * @param string $url API endpoint URL.
     * @param array $data Request data.
     * @return string API response or error message.
     */
    private function sendRequest($url, array $data)
    {
        try {
            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $data
            ]);

            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                return $e->getResponse()->getBody()->getContents();
            } else {
                return $e->getMessage();
            }
        }
    }

    /**
     * Send a message with a document to a specific number.
     *
     * @param string $to Recipient number in international format.
     * @param string $documentUrl URL of the document to be sent.
     * @param string $filename Document file name.
     * @param string $caption (Optional) Caption for the document.
     * @return string API response.
     */
    public function sendDocument($to, $documentUrl, $filename, $caption = '')
    {
        $url = $this->phoneNumberId . '/messages';
        $data = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $to,
            "type" => "document",
            "document" => [
                "link" => $documentUrl,
                "filename" => $filename,
                "caption" => $caption
            ]
        ];

        return $this->sendRequest($url, $data);
    }
}