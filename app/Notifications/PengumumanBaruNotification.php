<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PengumumanBaruNotification extends Notification
{
    use Queueable;

    private $pengumuman;

    /**
     * Create a new notification instance.
     */
    public function __construct($pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting(' ')
            ->line('Terdapat pengumuman baru yang ditujukan pada anda, dengan detail berikut: ')
            ->line('Pengirim: ' . $this->pengumuman->dibuat_oleh->name)
            ->line('Judul: ' . $this->pengumuman->judul)
            ->line('Tanggal Dikirim: ' . $this->pengumuman->created_at->format('d-m-Y H:i:s'))
            ->line('Tanggal Deadline: ' . date('d-m-Y H:i:s', strtotime($this->pengumuman->waktu)))
            ->action('Open Link', env('SANCTUM_STATEFUL_DOMAINS') . '/home/' . $this->pengumuman->id);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
