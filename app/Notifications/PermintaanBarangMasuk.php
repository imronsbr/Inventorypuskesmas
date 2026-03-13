<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PermintaanBarang;

class PermintaanBarangMasuk extends Notification
{
    use Queueable;

    protected $permintaan;

    /**
     * Create a new notification instance.
     */
    public function __construct(PermintaanBarang $permintaan)
    {
        $this->permintaan = $permintaan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Permintaan Barang Habis Pakai Baru')
            ->line('Ada permintaan barang habis pakai baru yang memerlukan approval.')
            ->line('Pemohon: ' . $this->permintaan->user->name)
            ->line('Tanggal: ' . $this->permintaan->tanggal_pesanan->format('d/m/Y H:i'))
            ->line('Puskesmas: ' . $this->permintaan->puskesmas->nama)
            ->line('Ruang: ' . $this->permintaan->ruang->nama)
            ->action('Lihat Detail', url('/permintaan-barang/approval'))
            ->line('Silakan review dan approve permintaan barang habis pakai ini.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'permintaan_id' => $this->permintaan->id,
            'user_id' => $this->permintaan->user_id,
            'puskesmas_id' => $this->permintaan->puskesmas_id,
            'ruang_id' => $this->permintaan->ruang_id,
            'message' => 'Permintaan barang habis pakai baru dari ' . $this->permintaan->user->name,
        ];
    }
} 