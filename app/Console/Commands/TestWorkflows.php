<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use JetBrains\PhpStorm\NoReturn;
use Spatie\LaravelIgnition\Support\LaravelVersion;
use Workflow\WorkflowStub;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Workflows\CustomerWorkflow;
use function Psy\debug;

class TestWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:workflows';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Execute the console command.
     */
    #[NoReturn] public function handle(): void
    {
        User::factory()->count(5000)->create([
            'email_verified_at' => null,
        ]);

        $query = DB::connection('mysql')
            ->table('users')
            ->select('id')
            ->whereNull('email_verified_at');

        $query->lazyByIdDesc()->each(function ($customer)  {

            $workflow = WorkflowStub::make(CustomerWorkflow::class);
            $workflow->start($customer->id);

            $this->info("[{$workflow->id()}] Triggered workflow for Customer ID {$customer->id}");

        });

        $message = '[' . (memory_get_usage(true) / 1024 / 1024) . ' MB' . '] ' .  get_class($this);
        Log::debug($message);

        exit(0);

    }
}

