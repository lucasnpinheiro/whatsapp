<?php

require 'vendor/autoload.php';

use LucasNPinheiro\Whatsapp\WhatsAppSender;

$token = ''; // Informe o token de autenticação
$phoneNumberId = ''; // Informe o ID do número de telefone vinculado a sua conta do WhatsApp Business API
$to = ''; // Informe o número de telefone do destinatário no formato internacional
$documentUrl = 'https://example.com/document.pdf';
$filename = 'document.pdf';
$caption = ''; // Informe a legenda da mensagem

$whatsappSender = new WhatsAppSender($token, $phoneNumberId);
$response = $whatsappSender->sendDocument($to, $documentUrl, $filename, $caption);

echo $response;
