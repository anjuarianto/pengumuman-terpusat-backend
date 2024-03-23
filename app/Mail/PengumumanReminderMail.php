<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PengumumanReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    /**
     * Create a new message instance.
     */
    public function __construct($pengumuman)
    {
        $this->data = $pengumuman;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengumuman Reminder Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: '<h1>Reminder Pengumuman</h1>
                        <p>
                            Pengirim: ' . $this->data->dibuat_oleh->name . '<br>
                            Judul: ' . $this->data->judul . '<br>
                            Tanggal Dikirim: ' . $this->data->created_at->format('d-m-Y H:i:s') . '<br>
                            Tanggal Deadline: ' . date('d-m-Y', strtotime($this->data->waktu)) . '<br>
                            Ini adalah reminder pengumuman
                        </p>',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
