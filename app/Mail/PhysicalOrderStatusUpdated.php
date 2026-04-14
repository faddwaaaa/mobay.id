<?php

namespace App\Mail;

use App\Models\PhysicalOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PhysicalOrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public string $statusLabel;

    public function __construct(public PhysicalOrder $order, public string $biteshipStatus)
    {
        $this->statusLabel = match($biteshipStatus) {
            'picked_up'         => 'Paket Dijemput Kurir',
            'in_transit'        => 'Paket Dalam Perjalanan',
            'delivered'         => 'Paket Sudah Diterima! 🎉',
            'return_in_transit' => 'Paket Sedang Diretur',
            'returned'          => 'Paket Telah Diretur',
            default             => ucfirst(str_replace('_', ' ', $biteshipStatus)),
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update ' . $this->order->order_code . ': ' . $this->statusLabel,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.physical-orders.status-updated',
        );
    }
}