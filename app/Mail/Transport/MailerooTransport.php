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

    $response = Http::withHeaders([
        'X-Api-Key' => $this->apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://smtp.maileroo.com/api/v2/emails', [
        'from' => [
            'address' => $fromAddress->getAddress(),
            'name'    => $fromAddress->getName() ?? 'Non-UniPay',
        ],
        'to' => array_map(fn($a) => [
            'address' => $a->getAddress(),
            'name'    => $a->getName() ?? '',
        ], $email->getTo()),
        'subject'    => $email->getSubject(),
        'html_body'  => $email->getHtmlBody(),
        'plain_body' => $email->getTextBody() ?? strip_tags($email->getHtmlBody()),
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