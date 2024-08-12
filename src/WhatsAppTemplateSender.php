<?php

declare(strict_types=1);

namespace Whatsapp;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WhatsAppTemplateSender
{
    public function __construct(private $accessToken, private $businessPhoneNumberId, private $apiVersion = 'v20.0')
    {
        $this->client = new Client([
            'base_uri' => "https://graph.facebook.com/{$this->apiVersion}/{$businessPhoneNumberId}/",
            'headers' => [
                'Authorization' => "Bearer $this->accessToken",
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * @param $to
     * @param $templateName
     * @param $languageCode
     * @param $components
     * @return array
     */
    public function sendTemplateMessage($to, $templateName, $languageCode, $components): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ],
                'components' => $components
            ]
        ];

        try {
            $response = $this->client->post('messages', [
                'json' => $data
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new Exception('Erro ao enviar a mensagem: ' . $e->getMessage()); // Corrigido aqui
        }
    }

    /**
     * @param $to
     * @return array
     */
    public function helloWorld($to): array
    {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => 'hello_world',
                'language' => [
                    'code' => 'en_US'
                ]
            ]
        ];

        try {
            $response = $this->client->post('messages', [
                'json' => $data
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            throw new Exception('Erro ao enviar a mensagem: ' . $e->getMessage()); // Corrigido aqui
        }
    }


    /**
     * @param $text
     * @return array
     */
    public function buildTextHeaderComponent($text): array
    {
        return [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => $text
                ]
            ]
        ];
    }

    /**
     * @param $documentId
     * @param $filename
     * @return array
     */
    public function buildDocumentHeaderComponent($documentId, $filename = null): array
    {
        $component = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'document',
                    'document' => [
                        'id' => $documentId
                    ]
                ]
            ]
        ];

        if ($filename) {
            $component['parameters'][0]['document']['filename'] = $filename;
        }

        return $component;
    }

    /**
     * @param $link
     * @param $providerName
     * @param $filename
     * @return array
     */
    public function buildDocumentHeaderComponentByLink($link, $providerName = null, $filename = null): array
    {
        $component = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'document',
                    'document' => [
                        'link' => $link
                    ]
                ]
            ]
        ];

        if ($providerName) {
            $component['parameters'][0]['document']['provider'] = [
                'name' => $providerName
            ];
        }

        if ($filename) {
            $component['parameters'][0]['document']['filename'] = $filename;
        }

        return $component;
    }

    /**
     * @param $videoId
     * @return array
     */
    public function buildVideoHeaderComponent($videoId): array
    {
        return [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'video',
                    'video' => [
                        'id' => $videoId
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $link
     * @param $providerName
     * @return array
     */
    public function buildVideoHeaderComponentByLink($link, $providerName = null): array
    {
        $component = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'video',
                    'video' => [
                        'link' => $link
                    ]
                ]
            ]
        ];

        if ($providerName) {
            $component['parameters'][0]['video']['provider'] = [
                'name' => $providerName
            ];
        }

        return $component;
    }

    /**
     * @param $link
     * @param $providerName
     * @return array
     */
    public function buildImageHeaderComponentByLink($link, $providerName = null): array
    {
        $component = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'image',
                    'image' => [
                        'link' => $link
                    ]
                ]
            ]
        ];

        if ($providerName) {
            $component['parameters'][0]['image']['provider'] = [
                'name' => $providerName
            ];
        }

        return $component;
    }

    /**
     * @param $text
     * @return array
     */
    public function buildTextBodyComponent($text): array
    {
        return [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => $text
                ]
            ]
        ];
    }

    /**
     * @param $currencyCode
     * @param $amount
     * @param $fallbackValue
     * @return array
     */
    public function buildCurrencyBodyComponent($currencyCode, $amount, $fallbackValue): array
    {
        return [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'currency',
                    'currency' => [
                        'code' => $currencyCode,
                        'amount_1000' => $amount,
                        'fallback_value' => $fallbackValue
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $timestamp
     * @param $fallbackValue
     * @return array
     */
    public function buildDateTimeBodyComponent($timestamp, $fallbackValue = null): array
    {
        return [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'date_time',
                    'date_time' => [
                        'timestamp' => $timestamp,
                        'fallback_value' => $fallbackValue
                    ]
                ]
            ]
        ];
    }
}