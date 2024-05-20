<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class NotifikasiReplyBaruJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $comment;

    /**
     * Create a new job instance.
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipients = $this->comment->pengumuman->usersFromPengumumanTo->pluck('email')->toArray();
        $recipients[] = $this->comment->pengumuman->user->email;
        Notification::route('mail', $recipients)->notify(new \App\Notifications\ReplyPengumumanBaruNotification($this->comment));
    }
}
