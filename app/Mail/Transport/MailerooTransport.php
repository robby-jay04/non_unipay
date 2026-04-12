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

        $payload = [
            'from' => [
                'address' => array_key_first($email->getFrom() ? array_map(fn($a) => $a->getAddress(), $email->getFrom()) : []),
                'display_name' => $email->getFrom()[0]->getName() ?? '',
            ],
            'to' => array_map(fn($a) => ['address' => $a->getAddress(), 'display_name' => $a->getName()], $email->getTo()),
            'subject' => $email->getSubject(),
            'html' => $email->getHtmlBody(),
            'plain' => $email->getTextBody(),
        ];

        Http::withToken($this->apiKey)
            ->post('https://smtp.maileroo.com/api/v2/emails', $payload)
            ->throw();
    }

    public function __toString(): string
    {
        return 'maileroo';
    }
}