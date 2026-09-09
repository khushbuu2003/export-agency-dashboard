<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $suppliers;
    public $stocks;
    public $investors;

    public function __construct($suppliers, $stocks, $investors)
    {
        $this->suppliers = $suppliers;
        $this->stocks = $stocks;
        $this->investors = $investors;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Executive Reminder: Supplier Payments, Investor Collectables & Stock Audit',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment_reminder',
        );
    }
}
