<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected $transaction;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction, string $message)
    {
        $this->transaction = $transaction;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id'   => $this->transaction->id,
            'transaction_code' => 'GK-' . str_pad($this->transaction->id, 4, '0', STR_PAD_LEFT),
            'message'          => $this->message,
            'status'           => $this->transaction->status_transaksi,
            'url'              => url('/riwayat'),
        ];
    }
}
