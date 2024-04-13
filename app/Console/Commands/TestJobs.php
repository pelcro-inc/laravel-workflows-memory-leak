<?php

namespace App\Console\Commands;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Jobs\VerifyEmail;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Jobs\GenerateStrongPassword;

class TestJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = 1000;

        User::factory()->count($count)->create([
            'email_verified_at' => null,
        ]);

        $query = DB::connection('mysql')
            ->table('users')
            ->select('id')
            ->whereNull('email_verified_at');

        $jobs = [];
        $query->lazyByIdDesc()->each(function ($customer) use (&$jobs) {
            $customer = User::find($customer->id);
            $jobs[] = [
                new GenerateStrongPassword($customer),
                new VerifyEmail($customer),
            ];
        });

        $batch = Bus::batch($jobs)->then(static function (Batch $batch) {
            Log::channel('jobs')->debug(sprintf(
                '[%d] [%d] -> %s',
                getmypid(),
                (memory_get_usage(true) / 1024 / 1024),
                'Batch completed',
            ));
        })->catch(static function (Batch $batch, \Throwable $e) {
            // do nothing.
        })->finally(static function (Batch $batch) {
            // do nothing.
        })->name(Uuid::uuid7()->toString())->onConnection('redis-queue-default')->onQueue('jobs')->dispatch();

        $this->info("Dispatched {$count} chained jobs in batch (total of {$batch->totalJobs} jobs)");

        exit(0);

    }
}

