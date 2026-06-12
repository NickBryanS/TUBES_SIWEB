<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Notifications\RentalReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRentalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rentals:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to users whose rental ends tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $transactions = Transaction::whereDate('tanggal_selesai', $tomorrow)
            ->whereIn('status_transaksi', ['diproses', 'dikirim'])
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->user->notify(new RentalReminder($transaction));
        }

        $this->info(count($transactions) . ' reminder(s) sent successfully.');
    }
}
