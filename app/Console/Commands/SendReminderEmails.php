<?php

namespace App\Console\Commands;

use App\Jobs\ReminderPengumumanJob;
use App\Models\Pengumuman;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendReminderEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminder-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $pengumumanList = Pengumuman::notificationDaily();

        if ($pengumumanList) {
            foreach ($pengumumanList as $pengumuman) {
                $now = now()->timezone('Asia/Jakarta');

                ReminderPengumumanJob::dispatch($pengumuman, $pengumuman->type)->onQueue('default')->delay($now);
            }
        } else {
            $this->info('No pengumuman to send');
        }

    }
}
