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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orderCode = 'GK-' . str_pad($this->transaction->id, 4, '0', STR_PAD_LEFT);
        
        return (new MailMessage)
            ->subject('Notifikasi Pesanan ' . $orderCode)
            ->greeting('Halo, ' . ($notifiable->nama_lengkap ?? 'Pelanggan') . '!')
            ->line($this->message)
            ->action('Lihat Detail Pesanan', url('/riwayat'))
            ->line('Terima kasih atas kepercayaan Anda menyewa di Gardakala Outdoor!');
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
