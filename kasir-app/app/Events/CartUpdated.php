<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class CartUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $cart;
    public int $total;
    public int $subtotal;
    public int $discount;
    public string $cashier;
    public string $payment;
    public int $paid;
    public int $change;
    public ?string $qr;
    public ?string $status;

    // 🔥 TAMBAHAN
    public ?string $va;
    public ?string $bank;

    public function __construct(
        $cart = [],
        $total = 0,
        $subtotal = 0,
        $discount = 0,
        $cashier = '-',
        $payment = '-',
        $paid = 0,
        $change = 0,
        $qr = null,
        $status = null,

        // 🔥 TAMBAHAN
        $va = null,
        $bank = null
    )
    {
        $this->cart = is_array($cart) ? $cart : [];
        $this->total = (int) $total;
        $this->subtotal = (int) $subtotal;
        $this->discount = (int) $discount;
        $this->cashier = (string) $cashier;
        $this->payment = (string) $payment;
        $this->paid = (int) $paid;
        $this->change = (int) $change;

        $this->qr = $qr ? (string) $qr : null;
        $this->status = $status ? (string) $status : null;

        // 🔥 TAMBAHAN (AMAN)
        $this->va = $va ? (string) $va : null;
        $this->bank = $bank ? (string) $bank : null;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('cart-channel');
    }

    public function broadcastAs(): string
    {
        return 'cart.updated';
    }

    /*
    |--------------------------------------------------------------------------
    | Data yang dikirim ke Echo / Pusher
    |--------------------------------------------------------------------------
    */
    public function broadcastWith(): array
    {
        return [
            'cart' => $this->cart ?? [],
            'total' => $this->total ?? 0,
            'subtotal' => $this->subtotal ?? 0,
            'discount' => $this->discount ?? 0,
            'cashier' => $this->cashier ?? '-',
            'payment' => $this->payment ?? '-',
            'paid' => $this->paid ?? 0,
            'change' => $this->change ?? 0,
            'qr' => $this->qr,
            'status' => $this->status,

            // 🔥 TAMBAHAN (PENTING)
            'va' => $this->va,
            'bank' => $this->bank,
        ];
    }
}