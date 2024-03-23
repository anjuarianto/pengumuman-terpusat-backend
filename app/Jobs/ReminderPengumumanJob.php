<?php

namespace App\Jobs;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PengumumanReminderMail;

class ReminderPengumumanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pengumumanList = Pengumuman::notificationDaily();

        foreach ($pengumumanList as $pengumuman) {
            $emails = $pengumuman->usersFromPengumumanTo->pluck('email')->toArray();
            
            // Check if the email has already been sent
            foreach ($emails as $email) {
                $sentEmail = DB::table('notifications_log')
                    ->where('email', $email)
                    ->where('pengumuman_id', $pengumuman->id)
                    ->where('type', '1 Day')
                    ->first();

                if (!$sentEmail) {
                    Mail::to($email)->send(new PengumumanReminderMail($pengumuman));

                    // Record the sent email
                    DB::table('notifications_log')->insert([
                        'email' => $email,
                        'pengumuman_id' => $pengumuman->id,
                        'type' => '1 Day',
                        'sent_at' => now(),
                    ]);
                }
            }

        }


    }
}
