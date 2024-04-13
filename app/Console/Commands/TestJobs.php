<?php

namespace App\Console\Commands;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Jobs\VerifyEmail;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
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
        User::factory()->count(1000)->create([
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
            //
        })->catch(static function (Batch $batch, \Throwable $e) {
            //
        })->finally(static function (Batch $batch) {
            //
        })->name(Uuid::uuid7()->toString())->onConnection('redis-queue-default')->onQueue('batches')->dispatch();

        $this->info('Total jobs: ' . $batch->totalJobs);
        $this->info('');

        /*
            $progress = $this->output->createProgressBar(100);

            while (($batch = $batch->fresh()) && !$batch->finished()) {
                $progress->setProgress($batch->progress());
                sleep(1);
            }

            $progress->finish();
        */

        exit(0);

    }
}

