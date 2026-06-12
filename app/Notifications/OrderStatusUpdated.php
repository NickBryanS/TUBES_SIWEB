<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
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
        $statusLabel = str_replace('_', ' ', $this->transaction->status_transaksi);
        
        return (new MailMessage)
            ->subject('Update Status Pesanan Anda ' . $orderCode)
            ->greeting('Halo, ' . ($notifiable->nama_lengkap ?? 'Pelanggan') . '!')
            ->line('Status untuk pesanan Anda dengan kode referensi **' . $orderCode . '** telah diperbarui.')
            ->line('Status Saat Ini: **' . strtoupper($statusLabel) . '**')
            ->action('Lihat Detail Pesanan', url('/riwayat'))
            ->line('Terima kasih telah menyewa peralatan outdoor di Gardakala Outdoor!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'status' => $this->transaction->status_transaksi,
            'message' => 'Status pesanan #GK-' . str_pad($this->transaction->id, 4, '0', STR_PAD_LEFT) . ' telah diubah menjadi ' . str_replace('_', ' ', $this->transaction->status_transaksi) . '.',
        ];
    }
}
