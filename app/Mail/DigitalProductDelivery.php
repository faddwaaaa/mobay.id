<?php

namespace App\Mail;

use App\Models\DigitalOrder;
use App\Models\DownloadToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DigitalProductDelivery extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public DigitalOrder $order,
        public DownloadToken $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Pesanan #' . $this->order->order_code . ' - File Siap Didownload!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.digital-product-delivery',
            with: [
                'order'       => $this->order,
                'token'       => $this->token,
                'downloadUrl' => route('download.verify', ['token' => $this->token->token]),
                'productName' => $this->order->product->name,
                'buyerName'   => $this->order->buyer_name,
                'expiresAt'   => $this->token->expires_at->format('d M Y H:i'),
                'maxDownload' => $this->token->max_downloads,
            ],
        );
    }
}