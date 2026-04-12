<?php

namespace App\Mail\Transport;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Http;

class MailerooTransport extends AbstractTransport
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();
    }

   protected function doSend(SentMessage $message): void
{
    $email = MessageConverter::toEmail($message->getOriginalMessage());

    $fromAddress = $email->getFrom()[0];
    $toAddresses = array_map(fn($a) => $a->getAddress(), $email->getTo());

   $response = Http::withHeaders([
    'X-Api-Key' => $this->apiKey,
    'Content-Type' => 'application/json',
])->post('https://smtp.maileroo.com/api/v2/emails', [
    'from'         => $fromAddress->getName() 
                        ? $fromAddress->getName() . ' <' . $fromAddress->getAddress() . '>'
                        : $fromAddress->getAddress(),
    'to'           => implode(',', $toAddresses),
    'subject'      => $email->getSubject(),
    'html_body'    => $email->getHtmlBody(),
    'plain_body'   => $email->getTextBody() ?? strip_tags($email->getHtmlBody()),
]);
    if (!$response->successful()) {
        throw new \Exception('Maileroo API error: ' . $response->body());
    }
}

    public function __toString(): string
    {
        return 'maileroo';
    }
}