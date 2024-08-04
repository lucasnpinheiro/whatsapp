<?php
declare(strict_types=1);

namespace LucasNPinheiro\Whatsapp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * Classe para enviar mensagens via API do WhatsApp usando a biblioteca Guzzle.
 */
class WhatsAppSender
{
    /**
     * Token de autenticação para a API do WhatsApp.
     *
     * @var string
     */
    private $token;

    /**
     * ID do número de telefone usado para enviar mensagens.
     *
     * @var string
     */
    private $phoneNumberId;

    /**
     * Instância do cliente HTTP Guzzle.
     *
     * @var Client
     */
    private $client;

    /**
     * Construtor da classe WhatsAppSender.
     *
     * @param string $token Token de autenticação para a API do WhatsApp.
     * @param string $phoneNumberId ID do número de telefone usado para enviar mensagens.
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
     * Envia uma mensagem de modelo para um número específico.
     *
     * @param string $to Número do destinatário no formato internacional.
     * @param string $templateName Nome do modelo a ser enviado.
     * @param string $languageCode Código do idioma do modelo.
     * @return string Resposta da API.
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
     * Envia uma mensagem com um documento para um número específico.
     *
     * @param string $to Número do destinatário no formato internacional.
     * @param string $documentUrl URL do documento a ser enviado.
     * @param string $filename Nome do arquivo do documento.
     * @param string $caption (Opcional) Legenda para o documento.
     * @return string Resposta da API.
     */
    public function sendBoletoMessage($to, $documentUrl, $filename, $caption = '')
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

    /**
     * Envia uma requisição POST para a API do WhatsApp e trata as exceções.
     *
     * @param string $url URL do endpoint da API.
     * @param array $data Dados da requisição.
     * @return string Resposta da API ou mensagem de erro.
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
}