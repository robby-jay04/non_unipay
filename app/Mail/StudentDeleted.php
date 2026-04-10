<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public string $studentNo
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Student Account Has Been Removed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.student-deleted',
        );
    }
}