<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyPengumumanBaruNotification extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
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
            ->line('Terdapat balasan baru pada pengumuman dengan detail berikut: ')
            ->line('Judul Pengumuman: ' . $this->comment->pengumuman->judul)
            ->line('Tanggal Dikirim: ' . $this->comment->pengumuman->created_at->format('d-m-Y H:i:s'))
            ->line('Tanggal Deadline: ' . date('d-m-Y H:i:s', strtotime($this->comment->pengumuman->waktu)))
            ->line('')
            ->line('Pengirim: ' . $this->comment->user->name)
            ->line('Balasan: ' . $this->comment->comment)
            ->action('Open Link', env('SANCTUM_STATEFUL_DOMAINS') . '/home/' . $this->comment->pengumuman->id);
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
