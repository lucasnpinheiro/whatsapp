# WhatsApp
Script para envio de mensagem via WhatsApp

## Instalação
```bash
composer require lucasnpinheiro/whatsapp
```

## Exemplo de uso

### Envio de Documento

```php
<?php

require 'vendor/autoload.php';

use Whatsapp\WhatsAppTemplateSender;

$token = 'YOUR_ACCESS_TOKEN'; // Informe o token de autenticação
$phoneNumberId = 'YOUR_PHONE_NUMBER_ID'; // Informe o ID do número de telefone vinculado a sua conta do WhatsApp Business API
$to = 'RECIPIENT_PHONE_NUMBER'; // Informe o número de telefone do destinatário no formato internacional
$templateName = 'hello_world'; // Nome do template que deseja usar
$languageCode = 'en_US'; // Código de idioma do template
$components = []; // Componentes adicionais do template, se houver

$whatsappSender = new WhatsAppTemplateSender($token, $phoneNumberId);
$response = $whatsappSender->sendTemplateMessage($to, $templateName, $languageCode, $components);

echo $response;
```

### Envio de Mensagem Hello World

```php
<?php

require 'vendor/autoload.php';

use Whatsapp\WhatsAppTemplateSender;

$token = 'YOUR_ACCESS_TOKEN'; // Informe o token de autenticação
$phoneNumberId = 'YOUR_PHONE_NUMBER_ID'; // Informe o ID do número de telefone vinculado a sua conta do WhatsApp Business API
$to = 'RECIPIENT_PHONE_NUMBER'; // Informe o número de telefone do destinatário no formato internacional

$whatsappSender = new WhatsAppTemplateSender($token, $phoneNumberId);
$response = $whatsappSender->helloWorld($to);

echo $response;
```

### Observações

- Certifique-se de substituir `YOUR_ACCESS_TOKEN`, `YOUR_PHONE_NUMBER_ID`, e `RECIPIENT_PHONE_NUMBER` pelos valores corretos.
- O método `sendTemplateMessage` exige que você configure o template e os componentes de acordo com a sua necessidade.
- O método `helloWorld` é um exemplo simples de como enviar uma mensagem de template padrão para testar a conexão com o serviço do WhatsApp.

Se precisar de mais informações ou ajustes, é só avisar!