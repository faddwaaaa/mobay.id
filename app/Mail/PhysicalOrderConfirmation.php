<?php

namespace App\Mail;

use App\Models\PhysicalOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PhysicalOrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PhysicalOrder $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan ' . $this->order->order_code . ' Berhasil Dibuat! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.physical-orders.confirmation',
        );
    }
}