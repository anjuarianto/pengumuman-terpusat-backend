<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class KirimEmailPengumumanBaruJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pengumuman;

    /**
     * Create a new job instance.
     */
    public function __construct($pengumuman)
    {
        $this->pengumuman = $pengumuman;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipients = $this->pengumuman->usersFromPengumumanTo->pluck('email')->toArray();
        $recipients[] = $this->pengumuman->user->email;
        Notification::route('mail', $recipients)->notify(new \App\Notifications\PengumumanBaruNotification($this->pengumuman));
    }
}
